<?php
class CmThizer {
  
  private $template = 'template.phtml';
  
  private $landingPage = 'landing-page.phtml';
  
  private $uri;
  
  private $params = array();
  
  private $post = array();
  
  private $routes = array();
  
  public function __construct() {
    try {
      
      $this->uri = new \CmThizer\Uri();
      
      // Resolver configuracoes
      $this->resolveParams();
      $this->resolvePost();
      $this->resolveRoutes();
      
      // Check if route exists
      if (!isset($this->routes[$this->uri->getRoute()])) {
        throw new Exception("404 - Page not found", 404);
      }
      
      // Variables to be appended to the view
      $route = $this->routes[$this->uri->getRoute()];
      $configs = $route['configs'];
      $basePath = $this->uri->getBasePath();
      
      // Load content
      $content = "";
      if ($route['content'] && file_exists($route['content'])) {
        
        // Allowed to read file?
        if (is_readable($route['content'])) {
          $parseDown = new Parsedown();
          $content = $parseDown->parse(file_get_contents($route['content']));
          
        } else {
          throw new Exception("Markdown content file ({$route['content']}) is not readable");
        }
      }
      
      // Including here, all these variables defined above
      // are accessible on the view
      include './site/'.$configs['template'];
      
    } catch (Exception $ex) {
      dump($ex);
    } catch (Error $err) {
      dump($err);
    }
  }
  
  public function getUrl($link = ''): string {
    return getenv('REQUEST_SCHEME').'://'.getenv('HTTP_HOST').$this->uri->getBasePath().'/'. trim($link, '/');
  }
  
  public function setTemplate($name): CmThizer {
    $this->template = $name.'.phtml';
    return $this;
  }
  
  public function getTemplate(): string {
    return $this->template;
  }
  
  public function setLandingPage($name): CmThizer {
    $this->landingPage = $name.'.phtml';
    return $this;
  }
  
  public function getLandingPage(): string {
    return $this->landingPage;
  }
  
  public function getUri(): \CmThizer\Uri {
    return $this->uri;
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
    
    $site = './site';
    $dirItems = $this->scandirRecursive($site);
    
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
   * Nos da a lista de conteudo da pasta site
   * 
   * ## RECURSIVA ##
   */
  private function scandirRecursive($dirname): array {
    $items = array();
    if (is_dir($dirname)) {
      foreach (scandir($dirname) as $item) {
        if (!in_array($item, array('.', '..'))) {
          if (is_dir($dirname.DIRECTORY_SEPARATOR.$item)) {
            $items[$dirname.DIRECTORY_SEPARATOR.$item] = $this->scandirRecursive($dirname.DIRECTORY_SEPARATOR.$item);
          } else {
            $items[] = $item;
          }
        }
      }
    }
    return $items;
  }
}

