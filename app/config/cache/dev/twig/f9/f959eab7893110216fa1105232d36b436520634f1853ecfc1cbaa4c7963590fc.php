<?php

/* @WebProfiler/Collector/router.html.twig */
class __TwigTemplate_32b15e7ea0ab1c2453acc70f5d15727ff9139c5150e30ce2692b6421be46c5f2 extends Twig_Template
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
        $__internal_a1de5a2c038b84b1157973f9efb8ad58c1344addc7afdeb6cb3572c265c71175 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_a1de5a2c038b84b1157973f9efb8ad58c1344addc7afdeb6cb3572c265c71175->enter($__internal_a1de5a2c038b84b1157973f9efb8ad58c1344addc7afdeb6cb3572c265c71175_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "@WebProfiler/Collector/router.html.twig"));

        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_a1de5a2c038b84b1157973f9efb8ad58c1344addc7afdeb6cb3572c265c71175->leave($__internal_a1de5a2c038b84b1157973f9efb8ad58c1344addc7afdeb6cb3572c265c71175_prof);

    }

    // line 3
    public function block_toolbar($context, array $blocks = array())
    {
        $__internal_e924df32a796a8378136c1a64f7ae4e8fff1df1b81f295ca2e652f31607aa098 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_e924df32a796a8378136c1a64f7ae4e8fff1df1b81f295ca2e652f31607aa098->enter($__internal_e924df32a796a8378136c1a64f7ae4e8fff1df1b81f295ca2e652f31607aa098_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "toolbar"));

        
        $__internal_e924df32a796a8378136c1a64f7ae4e8fff1df1b81f295ca2e652f31607aa098->leave($__internal_e924df32a796a8378136c1a64f7ae4e8fff1df1b81f295ca2e652f31607aa098_prof);

    }

    // line 5
    public function block_menu($context, array $blocks = array())
    {
        $__internal_66290787efc67f65c9e0143ed53173ccf4a6d4da20d3040dd5827328a193e167 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_66290787efc67f65c9e0143ed53173ccf4a6d4da20d3040dd5827328a193e167->enter($__internal_66290787efc67f65c9e0143ed53173ccf4a6d4da20d3040dd5827328a193e167_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "menu"));

        // line 6
        echo "<span class=\"label\">
    <span class=\"icon\">";
        // line 7
        echo twig_include($this->env, $context, "@WebProfiler/Icon/router.svg");
        echo "</span>
    <strong>Routing</strong>
</span>
";
        
        $__internal_66290787efc67f65c9e0143ed53173ccf4a6d4da20d3040dd5827328a193e167->leave($__internal_66290787efc67f65c9e0143ed53173ccf4a6d4da20d3040dd5827328a193e167_prof);

    }

    // line 12
    public function block_panel($context, array $blocks = array())
    {
        $__internal_af708409895cb173f5214b6c9a9ae4da8744efbd924640d140bb60a99b30d61f = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_af708409895cb173f5214b6c9a9ae4da8744efbd924640d140bb60a99b30d61f->enter($__internal_af708409895cb173f5214b6c9a9ae4da8744efbd924640d140bb60a99b30d61f_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "panel"));

        // line 13
        echo "    ";
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\HttpKernelExtension')->renderFragment($this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("_profiler_router", array("token" => (isset($context["token"]) ? $context["token"] : $this->getContext($context, "token")))));
        echo "
";
        
        $__internal_af708409895cb173f5214b6c9a9ae4da8744efbd924640d140bb60a99b30d61f->leave($__internal_af708409895cb173f5214b6c9a9ae4da8744efbd924640d140bb60a99b30d61f_prof);

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
", "@WebProfiler/Collector/router.html.twig", "C:\\wamp\\www\\RogerMaireLocal\\vendor\\symfony\\symfony\\src\\Symfony\\Bundle\\WebProfilerBundle\\Resources\\views\\Collector\\router.html.twig");
    }
}
