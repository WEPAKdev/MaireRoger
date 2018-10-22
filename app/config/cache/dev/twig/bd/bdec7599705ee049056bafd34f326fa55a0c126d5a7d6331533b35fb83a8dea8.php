<?php

/* PrestaShopBundle:Admin/Security:compromised.html.twig */
class __TwigTemplate_ca5fc43bb7c62036b7bbe2e53627e21f9e48b2a5d9698b4a1c41a39b1c4f9b06 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 25
        $this->parent = $this->loadTemplate("::base.html.twig", "PrestaShopBundle:Admin/Security:compromised.html.twig", 25);
        $this->blocks = array(
            'stylesheets' => array($this, 'block_stylesheets'),
            'title' => array($this, 'block_title'),
            'body' => array($this, 'block_body'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "::base.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $__internal_4945ce522c8e41e36bbd65bb79885cd560ba6e4a75330986ec826c89347db392 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_4945ce522c8e41e36bbd65bb79885cd560ba6e4a75330986ec826c89347db392->enter($__internal_4945ce522c8e41e36bbd65bb79885cd560ba6e4a75330986ec826c89347db392_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "PrestaShopBundle:Admin/Security:compromised.html.twig"));

        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_4945ce522c8e41e36bbd65bb79885cd560ba6e4a75330986ec826c89347db392->leave($__internal_4945ce522c8e41e36bbd65bb79885cd560ba6e4a75330986ec826c89347db392_prof);

    }

    // line 27
    public function block_stylesheets($context, array $blocks = array())
    {
        $__internal_06676cb78c195dbe45c1153b9a85fe0ca6ccf1714be811ce2c43f123799c3a13 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_06676cb78c195dbe45c1153b9a85fe0ca6ccf1714be811ce2c43f123799c3a13->enter($__internal_06676cb78c195dbe45c1153b9a85fe0ca6ccf1714be811ce2c43f123799c3a13_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "stylesheets"));

        // line 28
        echo "  <link rel=\"stylesheet\" href=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("themes/new-theme/public/theme.css"), "html", null, true);
        echo "\" />
  <style>
    .fake-button {
      height: 40px;
      line-height: 30px;
    }

    #csrf-white-container div:first-child {
      background: white;
      padding: 50px;
    }

    #security-compromised-page h1 {
      padding-top: 40px;
      padding-bottom: 40px;
    }
  </style>
";
        
        $__internal_06676cb78c195dbe45c1153b9a85fe0ca6ccf1714be811ce2c43f123799c3a13->leave($__internal_06676cb78c195dbe45c1153b9a85fe0ca6ccf1714be811ce2c43f123799c3a13_prof);

    }

    // line 47
    public function block_title($context, array $blocks = array())
    {
        $__internal_26c2395cb148ad1bf7d716b6dcf746f48464bac1ab60f6589e16b4a007052144 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_26c2395cb148ad1bf7d716b6dcf746f48464bac1ab60f6589e16b4a007052144->enter($__internal_26c2395cb148ad1bf7d716b6dcf746f48464bac1ab60f6589e16b4a007052144_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "title"));

        // line 48
        echo "  ";
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Invalid token", array(), "Admin.Catalog.Help"), "html", null, true);
        echo "
";
        
        $__internal_26c2395cb148ad1bf7d716b6dcf746f48464bac1ab60f6589e16b4a007052144->leave($__internal_26c2395cb148ad1bf7d716b6dcf746f48464bac1ab60f6589e16b4a007052144_prof);

    }

    // line 50
    public function block_body($context, array $blocks = array())
    {
        $__internal_632ddf91bed0916e7f9ce0c02ef5337113123601ad05a7a9ceff665bddc7d214 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_632ddf91bed0916e7f9ce0c02ef5337113123601ad05a7a9ceff665bddc7d214->enter($__internal_632ddf91bed0916e7f9ce0c02ef5337113123601ad05a7a9ceff665bddc7d214_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "body"));

        // line 51
        echo "  <div class=\"fluid-container\" id=\"security-compromised-page\" >
      <div id=\"csrf-white-container\" class=\"col-md-offset-1 col-md-10\">
        <div class=\"col-md-10 col-md-offset-1\">
          <div class=\"alert alert-danger\" role=\"alert\">
            <i class=\"material-icons\">error_outline</i>
            <p>
              ";
        // line 57
        echo twig_replace_filter($this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("[1]Invalid token[/1]: direct access to this link may lead to a potential security breach.", array(), "Admin.Catalog.Help"), array("[1]" => "<b>", "[/1]" => "</b>"));
        echo "
            </p>
          </div>

          <h1 class=\"text-md-center\">";
        // line 61
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Do you want to display this page?", array(), "Admin.Catalog.Help"), "html", null, true);
        echo "</h1>
          <div class=\"col-md-8 col-md-offset-3\">
            <a class=\"btn btn-danger-outline fake-button col-md-4\" href=\"";
        // line 63
        echo twig_escape_filter($this->env, (isset($context["requestUri"]) ? $context["requestUri"] : $this->getContext($context, "requestUri")), "html", null, true);
        echo "\">
              ";
        // line 64
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Yes, I understand the risks", array(), "Admin.Catalog.Help"), "html", null, true);
        echo "
            </a>
            <a class=\"btn btn-primary fake-button col-md-4 col-md-offset-1\" href=\"";
        // line 66
        echo twig_escape_filter($this->env, $this->env->getExtension('PrestaShopBundle\Twig\LayoutExtension')->getAdminLink("AdminDashboard"), "html", null, true);
        echo "\">
              ";
        // line 67
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Take me out of there!", array(), "Admin.Catalog.Help"), "html", null, true);
        echo "
            </a>
          </div>
        </div>
      </div>
  </div>
";
        
        $__internal_632ddf91bed0916e7f9ce0c02ef5337113123601ad05a7a9ceff665bddc7d214->leave($__internal_632ddf91bed0916e7f9ce0c02ef5337113123601ad05a7a9ceff665bddc7d214_prof);

    }

    public function getTemplateName()
    {
        return "PrestaShopBundle:Admin/Security:compromised.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  123 => 67,  119 => 66,  114 => 64,  110 => 63,  105 => 61,  98 => 57,  90 => 51,  84 => 50,  74 => 48,  68 => 47,  42 => 28,  36 => 27,  11 => 25,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("{#**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *#}
{% extends '::base.html.twig' %}

{% block stylesheets %}
  <link rel=\"stylesheet\" href=\"{{ asset('themes/new-theme/public/theme.css') }}\" />
  <style>
    .fake-button {
      height: 40px;
      line-height: 30px;
    }

    #csrf-white-container div:first-child {
      background: white;
      padding: 50px;
    }

    #security-compromised-page h1 {
      padding-top: 40px;
      padding-bottom: 40px;
    }
  </style>
{% endblock %}

{% block title %}
  {{ 'Invalid token'|trans({},'Admin.Catalog.Help' ) }}
{% endblock %}
{% block body %}
  <div class=\"fluid-container\" id=\"security-compromised-page\" >
      <div id=\"csrf-white-container\" class=\"col-md-offset-1 col-md-10\">
        <div class=\"col-md-10 col-md-offset-1\">
          <div class=\"alert alert-danger\" role=\"alert\">
            <i class=\"material-icons\">error_outline</i>
            <p>
              {{ '[1]Invalid token[/1]: direct access to this link may lead to a potential security breach.'|trans({},'Admin.Catalog.Help')|replace({'[1]' : '<b>', '[/1]' : '</b>'})|raw }}
            </p>
          </div>

          <h1 class=\"text-md-center\">{{ 'Do you want to display this page?'|trans({},'Admin.Catalog.Help' ) }}</h1>
          <div class=\"col-md-8 col-md-offset-3\">
            <a class=\"btn btn-danger-outline fake-button col-md-4\" href=\"{{ requestUri }}\">
              {{ 'Yes, I understand the risks'|trans({},'Admin.Catalog.Help' ) }}
            </a>
            <a class=\"btn btn-primary fake-button col-md-4 col-md-offset-1\" href=\"{{ getAdminLink(\"AdminDashboard\") }}\">
              {{ 'Take me out of there!'|trans({},'Admin.Catalog.Help' ) }}
            </a>
          </div>
        </div>
      </div>
  </div>
{% endblock %}
", "PrestaShopBundle:Admin/Security:compromised.html.twig", "C:\\wamp64\\www\\RogerMaireLocal\\src\\PrestaShopBundle/Resources/views/Admin/Security/compromised.html.twig");
    }
}
