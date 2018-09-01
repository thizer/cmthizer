<?php
include_once 'config.php';

use CmThizer\Plugins\AbstractPlugin;
use CmThizer\Plugins\LoadPlugins;
use CmThizer\Uri;

class CmThizer {
  
  private $running = false;
  
  private $template = 'template.phtml';
  
  private $landingPage = 'landing-page.phtml';
  
  private $sitePath = './site/';
  
  private $pluginsPath = './plugins/';
  
  private $plugins;
  
  private $uri;
  
  private $params = array();
  
  private $post = array();
  
  private $routes = array();
  
  public function __construct() {
    try {
      $this->plugins = new LoadPlugins($this->pluginsPath, $this);
      
      // Resolve configuracoes de URL, DocumentRoot e BasePath 
      $this->plugins->dispatch(AbstractPlugin::PRE_URI);
      $this->uri = new Uri();
      $this->plugins->dispatch(AbstractPlugin::POS_URI);
      
      // Resolve configuracoes de parametros de URL (GET)
      $this->plugins->dispatch(AbstractPlugin::PRE_PARAMS);
      $this->resolveParams();
      $this->plugins->dispatch(AbstractPlugin::POS_PARAMS);
      
      // Resolve configuracoes de argumentos POST
      $this->plugins->dispatch(AbstractPlugin::PRE_POST);
      $this->resolvePost();
      $this->plugins->dispatch(AbstractPlugin::POS_POST);
      
      $this->plugins->dispatch(AbstractPlugin::PRE_ROUTES);
      $this->resolveRoutes();
      $this->plugins->dispatch(AbstractPlugin::POS_ROUTES);
      
    } catch (Exception $ex) {
      dump($ex);
    } catch (Error $err) {
      dump($err);
    }
  }
  
  public function run(): CmThizer {
    try {
      // Avoid a second call to this method
      if ($this->isRunning()) {
        return $this;
      }
      $this->running = true;
      
      // Call user PRE_RUN plugins
      $this->plugins->dispatch(AbstractPlugin::PRE_RUN);
      
      // Check if route exists
      if (!isset($this->routes[$this->uri->getRouteName()])) {
        throw new Exception("404 - Page not found", 404);
      }
      
      /**
       * Valores padrao para algumas variaveis que serao
       * visiveis nas views
       */
      
      $template = $this->template;
      
      // Variables to be appended to the view
      $route = $this->routes[$this->uri->getRouteName()];
      if (isset($route['configs'])) {
        foreach ($route['configs'] as $varName => $varValue) {
          $$varName = $varValue;
        }
      }
      
      // Caminho base
      $basePath = $this->uri->getBasePath();
      $baseUrl = $this->getBaseUrl();
      
      // Load content
      $content = "";
      if ($route['content'] && file_exists($route['content'])) {
        
        // Allowed to read file?
        if (is_readable($route['content'])) {
          $parseDown = new ParsedownExtra();
          $content = $parseDown->parse(file_get_contents($route['content']));
          
        } else {
          throw new Exception("Markdown content file ({$route['content']}) is not readable");
        }
      }
      
      // Including here, all these variables defined above
      // are accessible on the view
      include $this->sitePath.$template;
      
      // Com isso o editor nao marca essas
      // variaveis como nao utilizadas. Ou seja,
      // isso aqui nao serve para nada.
      unset($basePath);
      unset($baseUrl);
      unset($content);
      
      // Call user POS_RUN plugins
      $this->plugins->dispatch(AbstractPlugin::POS_RUN);
      
    } catch (Exception $ex) {
      dump($ex);
    }
    return $this;
  }
  
  private function resolveParams(): CmThizer {
    $this->params = (array) filter_input_array(INPUT_GET);
    return $this;
  }
  
  private function resolvePost(): CmThizer {
    $this->post = (array) filter_input_array(INPUT_POST);
    return $this;
  }
  
  private function resolveRoutes(): CmThizer {
    
    $dirItems = scandir_recursive($this->sitePath);
    
    /**
     * Outra recursiva, agora para organizar os dados da pagina
     * 
     * ## RECURSIVA ##
     */
    function resolve(array $items): array {
      $routes = array();
      
      $defaultValues = array(
        'title' => 'My website',
        'uri' => '/',
        'template' => 'template.phtml'
      );
      
      foreach ($items as $folder => $content) {
        if (is_dir($folder) && in_array('config.json', $content) && in_array('content.md', $content)) {
          
          // Get configs from config.json file
          $config['configs'] = array_merge(
            $defaultValues,
            json_decode(file_get_contents($folder.'/config.json'), true)
          );
          
          $config['content'] = $folder.'/content.md';
          
          $uri = '/'.ltrim($config['configs']['uri'], '/');
          
          $routes[$uri] = $config;
        } else if(is_array($content)) {
          $routes += resolve($content);
        }
      }
      return $routes;
    }
    $this->routes = resolve($dirItems);
    
    // If was not created a home landing page, we do it
    if (!isset($this->routes['/'])) {
      $this->routes['/'] = array(
          'configs' => array(
            'title' => 'My website',
            'uri' => '/',
            'template' => 'landing-page.phtml'
          ),
          'content' => ''
      );
    }
    
    return $this;
  }
  
  /**
   * User access methods (accessibles in plugins too)
   * All the methods below (or most of then) was designed
   * to be accessed in views or plugins files
   */
  
  /**
   * 
   * @param string $link
   * @return string
   */
  public function getUrl(string $link = ''): string {
    return getenv('REQUEST_SCHEME').'://'.getenv('HTTP_HOST').$this->uri->getBasePath().'/'. trim($link, '/');
  }
  
  public function getBaseUrl(string $link = ''): string {
    return $this->getUrl($link);
  }
  
  public function url(string $link = ''): string {
    return $this->getUrl($link);
  }
  
  /**
   * Alias to $this->uri->getBasePath()
   * 
   * @return string
   */
  public function getBasePath(): string {
    return $this->uri->getBasePath();
  }
  
  public function setTemplate(string $name): CmThizer {
    $this->template = $name.'.phtml';
    return $this;
  }
  
  public function getTemplate(): string {
    return $this->template;
  }
  
  public function setLandingPage(string $name): CmThizer {
    $this->landingPage = $name.'.phtml';
    return $this;
  }
  
  public function getLandingPage(): string {
    return $this->landingPage;
  }
  
  public function setSitePath(string $foldername): CmThizer {
    $this->sitePath = $foldername;
    return $this;
  }
  
  public function getSitePath(): string {
    return $this->sitePath;
  }
  
  public function getUri(): Uri {
    return $this->uri;
  }
  
  public function setPluginsPath(string $foldername): CmThizer {
    $this->pluginsPath = $foldername;
  }
  
  public function getPluginsPath(): string {
    return $this->pluginsPath;
  }
  
  public function getParams(): array {
    return $this->params;
  }
  
  public function getParam(string $name, $default = false) {
    $result = $default;
    if (isset($this->params[$name])) {
      $result = $this->params[$name];
    }
    return $result;
  }
  
  public function isPost(): bool {
    return (getenv('REQUEST_METHOD') == 'POST');
  }
  
  public function getPost(string $name = null, $default = false) {
    $result = $this->post;
    if ($name) {
      $result = $default;
      if (isset($this->post[$name])) {
        $result = $this->post[$name];
      }
    }
    return $result;
  }
  
  public function getRoutes(): array {
    return $this->routes;
  }
  
  public function getCurrentRoute(): array {
    return $this->routes[$this->getUri()->getRouteName()];
  }
  
  public function addViewVar(string $name, $value): CmThizer {
    $this->routes[$this->getUri()->getRouteName()]['configs'][$name] = $value;
    return $this;
  }
  
  public function isRunning(): bool {
    return $this->running;
  }
}

