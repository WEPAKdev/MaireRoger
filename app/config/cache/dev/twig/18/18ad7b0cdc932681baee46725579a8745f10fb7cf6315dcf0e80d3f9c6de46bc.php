<?php

/* @Twig/Exception/exception_full.html.twig */
class __TwigTemplate_a3f4263cbb9d1c113da0ad5d1edffc6c1da4325aee8ae2d11d84fe59b4121dc8 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("@Twig/layout.html.twig", "@Twig/Exception/exception_full.html.twig", 1);
        $this->blocks = array(
            'head' => array($this, 'block_head'),
            'title' => array($this, 'block_title'),
            'body' => array($this, 'block_body'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@Twig/layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $__internal_23f0f66dbbbc07a2f01885bc12ce109d4ca92a51ebe83108471f542e7d4cf1c0 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_23f0f66dbbbc07a2f01885bc12ce109d4ca92a51ebe83108471f542e7d4cf1c0->enter($__internal_23f0f66dbbbc07a2f01885bc12ce109d4ca92a51ebe83108471f542e7d4cf1c0_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "@Twig/Exception/exception_full.html.twig"));

        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_23f0f66dbbbc07a2f01885bc12ce109d4ca92a51ebe83108471f542e7d4cf1c0->leave($__internal_23f0f66dbbbc07a2f01885bc12ce109d4ca92a51ebe83108471f542e7d4cf1c0_prof);

    }

    // line 3
    public function block_head($context, array $blocks = array())
    {
        $__internal_53d510721a7c28bfa80431d6910be50ff0db2145e4f8beb752d3101e7fa7e6f4 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_53d510721a7c28bfa80431d6910be50ff0db2145e4f8beb752d3101e7fa7e6f4->enter($__internal_53d510721a7c28bfa80431d6910be50ff0db2145e4f8beb752d3101e7fa7e6f4_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "head"));

        // line 4
        echo "    <link href=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\HttpFoundationExtension')->generateAbsoluteUrl($this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("bundles/framework/css/exception.css")), "html", null, true);
        echo "\" rel=\"stylesheet\" type=\"text/css\" media=\"all\" />
";
        
        $__internal_53d510721a7c28bfa80431d6910be50ff0db2145e4f8beb752d3101e7fa7e6f4->leave($__internal_53d510721a7c28bfa80431d6910be50ff0db2145e4f8beb752d3101e7fa7e6f4_prof);

    }

    // line 7
    public function block_title($context, array $blocks = array())
    {
        $__internal_ffb127115a630026551e088a58807d167e47b342726a72f6feeaa0195b519ed2 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_ffb127115a630026551e088a58807d167e47b342726a72f6feeaa0195b519ed2->enter($__internal_ffb127115a630026551e088a58807d167e47b342726a72f6feeaa0195b519ed2_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "title"));

        // line 8
        echo "    ";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["exception"]) ? $context["exception"] : $this->getContext($context, "exception")), "message", array()), "html", null, true);
        echo " (";
        echo twig_escape_filter($this->env, (isset($context["status_code"]) ? $context["status_code"] : $this->getContext($context, "status_code")), "html", null, true);
        echo " ";
        echo twig_escape_filter($this->env, (isset($context["status_text"]) ? $context["status_text"] : $this->getContext($context, "status_text")), "html", null, true);
        echo ")
";
        
        $__internal_ffb127115a630026551e088a58807d167e47b342726a72f6feeaa0195b519ed2->leave($__internal_ffb127115a630026551e088a58807d167e47b342726a72f6feeaa0195b519ed2_prof);

    }

    // line 11
    public function block_body($context, array $blocks = array())
    {
        $__internal_1d8e33e42a93ad8b3a2ba1c83c7f2d0515080b89c4854c4bb8e7d5ea7401affb = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_1d8e33e42a93ad8b3a2ba1c83c7f2d0515080b89c4854c4bb8e7d5ea7401affb->enter($__internal_1d8e33e42a93ad8b3a2ba1c83c7f2d0515080b89c4854c4bb8e7d5ea7401affb_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "body"));

        // line 12
        echo "    ";
        $this->loadTemplate("@Twig/Exception/exception.html.twig", "@Twig/Exception/exception_full.html.twig", 12)->display($context);
        
        $__internal_1d8e33e42a93ad8b3a2ba1c83c7f2d0515080b89c4854c4bb8e7d5ea7401affb->leave($__internal_1d8e33e42a93ad8b3a2ba1c83c7f2d0515080b89c4854c4bb8e7d5ea7401affb_prof);

    }

    public function getTemplateName()
    {
        return "@Twig/Exception/exception_full.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  78 => 12,  72 => 11,  58 => 8,  52 => 7,  42 => 4,  36 => 3,  11 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("{% extends '@Twig/layout.html.twig' %}

{% block head %}
    <link href=\"{{ absolute_url(asset('bundles/framework/css/exception.css')) }}\" rel=\"stylesheet\" type=\"text/css\" media=\"all\" />
{% endblock %}

{% block title %}
    {{ exception.message }} ({{ status_code }} {{ status_text }})
{% endblock %}

{% block body %}
    {% include '@Twig/Exception/exception.html.twig' %}
{% endblock %}
", "@Twig/Exception/exception_full.html.twig", "C:\\wamp\\www\\RogerMaireLocal\\vendor\\symfony\\symfony\\src\\Symfony\\Bundle\\TwigBundle\\Resources\\views\\Exception\\exception_full.html.twig");
    }
}
