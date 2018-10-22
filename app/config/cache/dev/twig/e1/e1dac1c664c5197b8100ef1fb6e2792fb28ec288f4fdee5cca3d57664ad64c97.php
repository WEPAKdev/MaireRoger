<?php

/* @WebProfiler/Collector/router.html.twig */
class __TwigTemplate_3410845b6dd8e617eed209ec1bb32e3a7f71ae2926a8d041d4c4392a7ac2f7e8 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("@WebProfiler/Profiler/layout.html.twig", "@WebProfiler/Collector/router.html.twig", 1);
        $this->blocks = array(
            'toolbar' => array($this, 'block_toolbar'),
            'menu' => array($this, 'block_menu'),
            'panel' => array($this, 'block_panel'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@WebProfiler/Profiler/layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $__internal_1722c71d4dc729e4ed8c37425b08207d4024bb6c70cf11d7bb79e58fb001f4c6 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_1722c71d4dc729e4ed8c37425b08207d4024bb6c70cf11d7bb79e58fb001f4c6->enter($__internal_1722c71d4dc729e4ed8c37425b08207d4024bb6c70cf11d7bb79e58fb001f4c6_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "@WebProfiler/Collector/router.html.twig"));

        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_1722c71d4dc729e4ed8c37425b08207d4024bb6c70cf11d7bb79e58fb001f4c6->leave($__internal_1722c71d4dc729e4ed8c37425b08207d4024bb6c70cf11d7bb79e58fb001f4c6_prof);

    }

    // line 3
    public function block_toolbar($context, array $blocks = array())
    {
        $__internal_3273f83538889603ffcb96a8579eccec1e3b5c6f3509d884874249fbefe6e730 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_3273f83538889603ffcb96a8579eccec1e3b5c6f3509d884874249fbefe6e730->enter($__internal_3273f83538889603ffcb96a8579eccec1e3b5c6f3509d884874249fbefe6e730_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "toolbar"));

        
        $__internal_3273f83538889603ffcb96a8579eccec1e3b5c6f3509d884874249fbefe6e730->leave($__internal_3273f83538889603ffcb96a8579eccec1e3b5c6f3509d884874249fbefe6e730_prof);

    }

    // line 5
    public function block_menu($context, array $blocks = array())
    {
        $__internal_9390451d7db89318be3035f196ed872c9c8df452b84170d5c47d39c9aba5b1fc = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_9390451d7db89318be3035f196ed872c9c8df452b84170d5c47d39c9aba5b1fc->enter($__internal_9390451d7db89318be3035f196ed872c9c8df452b84170d5c47d39c9aba5b1fc_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "menu"));

        // line 6
        echo "<span class=\"label\">
    <span class=\"icon\">";
        // line 7
        echo twig_include($this->env, $context, "@WebProfiler/Icon/router.svg");
        echo "</span>
    <strong>Routing</strong>
</span>
";
        
        $__internal_9390451d7db89318be3035f196ed872c9c8df452b84170d5c47d39c9aba5b1fc->leave($__internal_9390451d7db89318be3035f196ed872c9c8df452b84170d5c47d39c9aba5b1fc_prof);

    }

    // line 12
    public function block_panel($context, array $blocks = array())
    {
        $__internal_3429da85996d083047376585e04efaae48c53ff8dc20943790d51976ef884bd4 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_3429da85996d083047376585e04efaae48c53ff8dc20943790d51976ef884bd4->enter($__internal_3429da85996d083047376585e04efaae48c53ff8dc20943790d51976ef884bd4_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "panel"));

        // line 13
        echo "    ";
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\HttpKernelExtension')->renderFragment($this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("_profiler_router", array("token" => (isset($context["token"]) ? $context["token"] : $this->getContext($context, "token")))));
        echo "
";
        
        $__internal_3429da85996d083047376585e04efaae48c53ff8dc20943790d51976ef884bd4->leave($__internal_3429da85996d083047376585e04efaae48c53ff8dc20943790d51976ef884bd4_prof);

    }

    public function getTemplateName()
    {
        return "@WebProfiler/Collector/router.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  73 => 13,  67 => 12,  56 => 7,  53 => 6,  47 => 5,  36 => 3,  11 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("{% extends '@WebProfiler/Profiler/layout.html.twig' %}

{% block toolbar %}{% endblock %}

{% block menu %}
<span class=\"label\">
    <span class=\"icon\">{{ include('@WebProfiler/Icon/router.svg') }}</span>
    <strong>Routing</strong>
</span>
{% endblock %}

{% block panel %}
    {{ render(path('_profiler_router', { token: token })) }}
{% endblock %}
", "@WebProfiler/Collector/router.html.twig", "C:\\wamp64\\www\\RogerMaireLocal\\vendor\\symfony\\symfony\\src\\Symfony\\Bundle\\WebProfilerBundle\\Resources\\views\\Collector\\router.html.twig");
    }
}
