# Fomos assistir o jogo!{.text-success}

_{{ created }}_ - {{ author }}<br>
_Categoria:_ **{{ category }}**

No último final de semana fomos assistir ao jogo do sub19
entre Toledo e Coritiba.

Foi mara

{% for tag in tags %}
  <a class="btn btn-secondary btn-sm" href="#{{ tag }}">{{ tag }}</a>
{% endfor %}
