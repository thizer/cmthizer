# Plugins CmThizer
Você pode inserir aqui seus plugins. Os plugins tem capacidade de acessar as informações
do sistema em todas as etapas de configuração. Basta configurá-lo corretamente.

## Como fazer
 1. Criar uma pasta com o nome do seu plugin
 1. Criar a classe do seu plugin e extender a classe `CmThizer\Plugins\AbstractPlugin`
 1. Utilizar um dos métodos abstratos a medida de sua necessidade
   - `init()`
   - `preUri()`
   - `posUri()`
   - `preParams()`
   - `posParams()`
   - `prePost()`
   - `posPost()`
   - `preRoutes()`
   - `posRoutes()`

