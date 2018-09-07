<?php
include_once 'config.php';

use CmThizer\Uri;
use CmThizer\Plugins\AbstractPlugin;
use CmThizer\Plugins\LoadPlugins;

class CmThizer {
  
  private $running = false;
  
  private $template = 'layout.phtml';
  
  private $landingPage = 'landing-page.phtml';
  
  private $sitePath = './site/';
  
  private $pluginsPath = './plugins/';
  
  private $plugins;
  
  private $uri;
  
  private $params = array();
  
  private $post = array();
  
  private $routes = array();
  
  public function __construct() {
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
  }
  
  public function run(): self {
    // Avoid a second call to this method
    if ($this->isRunning()) {
      return $this;
    }
    $this->running = true;
    
    // Call user PRE_RUN plugins
    $this->plugins->dispatch(AbstractPlugin::PRE_RUN);
    
    /**
     * Valores padrao para algumas variaveis que serao
     * visiveis nas views
     */
    
    $template = $this->template;
    
    // Variables to be appended to the view
    $route = $this->getCurrentRoute();
    foreach ($route as $varName => $varValue) {
      $$varName = $varValue;
    }
    
    // Caminho base
    $basePath = $this->uri->getBasePath();
    $baseUrl = $this->baseUrl();
    
    // Load content
    $content = $route['content'];
    if ($route['content'] && file_exists($route['content'])) {
      
      if (!is_readable($route['content'])) {
        throw new Exception("Content file ({$route['content']}) does not exists or is not readable");
      }
      
      $fileExt = pathinfo($route['content'], PATHINFO_EXTENSION);
      
      if (in_array($fileExt, array('phtml', 'php', 'html'))) {
        
        ob_start();
        include $route['content'];
        $content = ob_get_clean();
        
      } else {
        // Allowed to read file?
        $parseDown = new ParsedownExtra();
        $content = $parseDown->parse(file_get_contents($route['content']));
      }
    }
    
    $content = (new Twig\Environment(
      new Twig\Loader\ArrayLoader(array('thetpl' => $content)), // Loader
      array('debug' => DEVELOPMENT) // Params
    ))->render('thetpl', $route);

    // Including here, all these variables defined above
    // are accessible on the view
    if ($template) {
      include $this->sitePath.$template;
    }
    
    // Com isso o editor nao marca essas
    // variaveis como nao utilizadas. Ou seja,
    // isso aqui nao serve para nada.
    unset($basePath);
    unset($baseUrl);
    unset($content);
    
    // Call user POS_RUN plugins
    $this->plugins->dispatch(AbstractPlugin::POS_RUN);
    
    return $this;
  }
  
  private function resolveParams(): self {
    $this->params = (array) filter_input_array(INPUT_GET);
    return $this;
  }
  
  private function resolvePost(): self {
    $this->post = (array) filter_input_array(INPUT_POST);
    return $this;
  }
  
  private function resolveRoutes(): self {
    
    $dirItems = scandir_recursive($this->sitePath);
    
    /**
     * Outra recursiva, agora para organizar os dados da pagina
     * 
     * ## RECURSIVA ##
     */
    
    $this->routes = $this->resolve($dirItems);
    
    // If was not created a home landing page, we do it
    if (!isset($this->routes['/'])) {
      $this->routes['/'] = array(
        'title' => 'My website',
        'uri' => '/',
        'template' => $this->landingPage,
        'content' => '',
        'dirname' => $this->getSitePath()
      );
    }
    
    // Check if route exists
    if (!isset($this->routes[$this->uri->getRouteName()])) {
      throw new Exception("Page not found", 404);
    }
    
    return $this;
  }
  
  private function resolve(array $items): array {
    $routes = array();
    
    $defaultValues = array(
      'title' => 'My website',
      'uri' => '/',
      'template' => $this->template
    );
    $config = array();
    foreach ($items as $folder => $content) {
      
      $fileTypes = array(
        'config.json',
        'content.php',
        'content.phtml',
        'content.html',
        'content.md'
      );
      
      // It's a folder and the qtd of valid files found is => than 2
      if (is_dir($folder) && is_array($content) && in_array_any($fileTypes, $content)) {
        
        // Get configs from config.json file
        $config = array_merge(
          $defaultValues,
          json_decode(file_get_contents($folder.'/config.json'), true)
          );
        
        $contentFile = false;
        foreach (scandir($folder) as $file) {
          if (pathinfo($file, PATHINFO_FILENAME) == 'content') {
            $contentFile = $folder.'/'.$file;
          }
        }
        
        $config['dirname'] = $folder;
        $config['content'] = $contentFile;
        
        $uri = '/'.ltrim($config['uri'], '/');
        $routes[$uri] = $config;
        
        // If there's folders here
        // its because theres sub pages
        foreach(array_keys($content) as $subFolder) {
          if (is_dir($subFolder)) {
            $routes += $this->resolve($content);
          }
        }
        
      } else if(is_dir($folder)) {
        $routes += $this->resolve($content);
      }
    }
    return $routes;
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
    $url = getenv('REQUEST_SCHEME').'://'.getenv('HTTP_HOST');
    
    $href = preg_replace("/\/{2,}/", '/', $this->uri->getBasePath().'/'.$link);
    
    return $url.'/'.trim($href, '/');
  }
  
  public function url(string $link = ''): string {
    return $this->getUrl($link);
  }
  
  public function getBaseUrl(string $link = ''): string {
    return $this->getUrl($link);
  }
  
  public function baseUrl(string $link = ''): string {
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
  
  public function setTemplate(string $name): self {
    $this->template = $name.'.phtml';
    return $this;
  }
  
  public function getTemplate(): string {
    return $this->template;
  }
  
  public function setLandingPage(string $name): self {
    $this->landingPage = $name.'.phtml';
    return $this;
  }
  
  public function getLandingPage(): string {
    return $this->landingPage;
  }
  
  public function setSitePath(string $foldername): self {
    $this->sitePath = $foldername;
    return $this;
  }
  
  public function getSitePath(): string {
    return $this->sitePath;
  }
  
  public function getUri(): Uri {
    return $this->uri;
  }
  
  public function getPlugins(): array {
    return $this->plugins->getAll();
  }
  
  public function getPlugin(string $name): AbstractPlugin {
    return $this->plugins->get($name);
  }
  
  public function setPluginsPath(string $foldername): self {
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
    $route = $this->routes[$this->getUri()->getRouteName()] ?? false;
    if (!$route) {
      throw new Exception("Page not found", 404);
    }
    return $route;
  }
  
  public function addViewVar(string $name, $value): self {
    if (!isset($this->routes[$this->getUri()->getRouteName()])) {
      throw new Exception("Page not found", 404);
    }
    
    $this->routes[$this->getUri()->getRouteName()][$name] = $value;
    return $this;
  }
  
  public function isRunning(): bool {
    return $this->running;
  }
}

