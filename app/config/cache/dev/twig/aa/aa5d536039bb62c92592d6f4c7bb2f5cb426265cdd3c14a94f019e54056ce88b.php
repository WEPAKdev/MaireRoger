<?php

/* PrestaShopBundle:Admin/Module:manage.html.twig */
class __TwigTemplate_226110931ed91d0428f95b5d8b83f92b403b2e6d60554dae03b2b0832c8e1177 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 25
        $this->parent = $this->loadTemplate("PrestaShopBundle:Admin:layout.html.twig", "PrestaShopBundle:Admin/Module:manage.html.twig", 25);
        $this->blocks = array(
            'stylesheets' => array($this, 'block_stylesheets'),
            'javascripts' => array($this, 'block_javascripts'),
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "PrestaShopBundle:Admin:layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $__internal_f742d3c457e6ff48eaf74151b29683a5814deaa7d6b060cd398d56ca3294c1ad = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_f742d3c457e6ff48eaf74151b29683a5814deaa7d6b060cd398d56ca3294c1ad->enter($__internal_f742d3c457e6ff48eaf74151b29683a5814deaa7d6b060cd398d56ca3294c1ad_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "PrestaShopBundle:Admin/Module:manage.html.twig"));

        // line 115
        $context["js_translatable"] = twig_array_merge(array("Bulk Action - One module minimum" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("You need to select at least one module to use the bulk action.", array(), "Admin.Modules.Notification"), "Bulk Action - Request not found" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("The action \"[1]\" is not available, impossible to perform your request.", array(), "Admin.Modules.Notification"), "Bulk Action - Request not available for module" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("The action [1] is not available for module [2]. Skipped.", array(), "Admin.Modules.Notification")),         // line 119
(isset($context["js_translatable"]) ? $context["js_translatable"] : $this->getContext($context, "js_translatable")));
        // line 25
        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_f742d3c457e6ff48eaf74151b29683a5814deaa7d6b060cd398d56ca3294c1ad->leave($__internal_f742d3c457e6ff48eaf74151b29683a5814deaa7d6b060cd398d56ca3294c1ad_prof);

    }

    // line 27
    public function block_stylesheets($context, array $blocks = array())
    {
        $__internal_317874e0c07ce130748362efb8e805f894c6618142d2fc3fece75004f0f7f9c6 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_317874e0c07ce130748362efb8e805f894c6618142d2fc3fece75004f0f7f9c6->enter($__internal_317874e0c07ce130748362efb8e805f894c6618142d2fc3fece75004f0f7f9c6_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "stylesheets"));

        // line 28
        echo "    ";
        $this->displayParentBlock("stylesheets", $context, $blocks);
        echo "
    <link rel=\"stylesheet\" href=\"";
        // line 29
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("themes/default/css/bundle/module/drop.css"), "html", null, true);
        echo "\" />
    <link rel=\"stylesheet\" href=\"";
        // line 30
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("themes/default/css/bundle/plugins/jquery.pstagger.css"), "html", null, true);
        echo "\" />
";
        
        $__internal_317874e0c07ce130748362efb8e805f894c6618142d2fc3fece75004f0f7f9c6->leave($__internal_317874e0c07ce130748362efb8e805f894c6618142d2fc3fece75004f0f7f9c6_prof);

    }

    // line 33
    public function block_javascripts($context, array $blocks = array())
    {
        $__internal_4b233f0b84df942b854f86fdd7283abf79ee81f52f0de42a68f3511b41b17316 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_4b233f0b84df942b854f86fdd7283abf79ee81f52f0de42a68f3511b41b17316->enter($__internal_4b233f0b84df942b854f86fdd7283abf79ee81f52f0de42a68f3511b41b17316_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "javascripts"));

        // line 34
        echo "    ";
        $this->displayParentBlock("javascripts", $context, $blocks);
        echo "
    <script src=\"";
        // line 35
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("themes/default/js/bundle/plugins/jquery.pstagger.js"), "html", null, true);
        echo "\"></script>
    <script src=\"";
        // line 36
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("themes/default/js/bundle/module/loader.js"), "html", null, true);
        echo "\"></script>
    <script src=\"";
        // line 37
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("themes/default/js/bundle/module/module_card.js"), "html", null, true);
        echo "\"></script>
    <script src=\"";
        // line 38
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("themes/default/js/bundle/module/module.js"), "html", null, true);
        echo "\"></script>
";
        
        $__internal_4b233f0b84df942b854f86fdd7283abf79ee81f52f0de42a68f3511b41b17316->leave($__internal_4b233f0b84df942b854f86fdd7283abf79ee81f52f0de42a68f3511b41b17316_prof);

    }

    // line 41
    public function block_content($context, array $blocks = array())
    {
        $__internal_9707fe0e0a78883a9db5428acc8ea108b9fca633763b8ad9c370597ec5f7707c = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_9707fe0e0a78883a9db5428acc8ea108b9fca633763b8ad9c370597ec5f7707c->enter($__internal_9707fe0e0a78883a9db5428acc8ea108b9fca633763b8ad9c370597ec5f7707c_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "content"));

        // line 42
        echo "    <div class=\"row\">
        <div class=\"col-lg-10 col-lg-offset-1\">
            ";
        // line 45
        echo "            ";
        $this->loadTemplate("PrestaShopBundle:Admin/Module/Includes:modal_confirm_bulk_action.html.twig", "PrestaShopBundle:Admin/Module:manage.html.twig", 45)->display($context);
        // line 46
        echo "            ";
        // line 47
        echo "            ";
        $this->loadTemplate("PrestaShopBundle:Admin/Module/Includes:modal_addons_connect.html.twig", "PrestaShopBundle:Admin/Module:manage.html.twig", 47)->display(array_merge($context, array("level" => (isset($context["level"]) ? $context["level"] : $this->getContext($context, "level")), "errorMessage" => (isset($context["errorMessage"]) ? $context["errorMessage"] : $this->getContext($context, "errorMessage")))));
        // line 48
        echo "            ";
        // line 49
        echo "            ";
        $this->loadTemplate("PrestaShopBundle:Admin/Module/Includes:modal_import.html.twig", "PrestaShopBundle:Admin/Module:manage.html.twig", 49)->display(array_merge($context, array("level" => (isset($context["level"]) ? $context["level"] : $this->getContext($context, "level")), "errorMessage" => (isset($context["errorMessage"]) ? $context["errorMessage"] : $this->getContext($context, "errorMessage")))));
        // line 50
        echo "            ";
        // line 51
        echo "            ";
        $this->loadTemplate("PrestaShopBundle:Admin/Module/Includes:menu_top.html.twig", "PrestaShopBundle:Admin/Module:manage.html.twig", 51)->display(array_merge($context, array("topMenuData" => (isset($context["topMenuData"]) ? $context["topMenuData"] : $this->getContext($context, "topMenuData")))));
        // line 52
        echo "            <div class=\"module-sorting-menu\">
                <div class=\"row\">
                    <div class=\"col-lg-12\">
                        ";
        // line 55
        if (((isset($context["level"]) ? $context["level"] : $this->getContext($context, "level")) > twig_constant("PrestaShopBundle\\Security\\Voter\\PageVoter::LEVEL_UPDATE"))) {
            // line 56
            echo "                            <div class=\"module-sorting module-bulk-actions pull-right\">
                                <select data-toggle=\"select2\" class=\"sort-component hidden-xs-up\">
                                  <option value=\"\" disabled selected>";
            // line 58
            echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Bulk Actions", array(), "Admin.Global"), "html", null, true);
            echo "</option>
                                  <option value=\"bulk-uninstall\">";
            // line 59
            echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Uninstall", array(), "Admin.Actions"), "html", null, true);
            echo "</option>
                                  <option value=\"bulk-disable\">";
            // line 60
            echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Disable", array(), "Admin.Actions"), "html", null, true);
            echo "</option>
                                  <option value=\"bulk-enable\">";
            // line 61
            echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Enable", array(), "Admin.Actions"), "html", null, true);
            echo "</option>
                                  <option value=\"bulk-reset\">";
            // line 62
            echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Reset", array(), "Admin.Actions"), "html", null, true);
            echo "</option>
                                  <option value=\"bulk-enable-mobile\">";
            // line 63
            echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Enable Mobile", array(), "Admin.Modules.Feature"), "html", null, true);
            echo "</option>
                                  <option value=\"bulk-disable-mobile\">";
            // line 64
            echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Disable Mobile", array(), "Admin.Modules.Feature"), "html", null, true);
            echo "</option>
                                </select>
                            </div>
                        ";
        }
        // line 68
        echo "                        <div class=\"module-sorting module-sorting-author pull-right\">
                            <select data-toggle=\"select2\" class=\"sort-component hidden-xs-up\">
                              <option value=\"\" disabled>- ";
        // line 70
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Sort by", array(), "Admin.Actions"), "html", null, true);
        echo " -</option>
                              <option value=\"name\">";
        // line 71
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Name", array(), "Admin.Global"), "html", null, true);
        echo "</option>
                              <option value=\"access-desc\" selected>";
        // line 72
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Last access", array(), "Admin.Modules.Feature"), "html", null, true);
        echo "</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            ";
        // line 80
        echo "            <div class=\"module-short-list\">
                <span class=\"module-search-result-wording\">";
        // line 81
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("%nbModules% installed modules", array("%nbModules%" => twig_length_filter($this->env, $this->getAttribute((isset($context["modules"]) ? $context["modules"] : $this->getContext($context, "modules")), "modules", array()))), "Admin.Modules.Feature"), "html", null, true);
        echo "</span>
                <span class=\"help-box\" data-toggle=\"popover\"
                  data-title=\"";
        // line 83
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Installed modules", array(), "Admin.Modules.Feature"), "html", null, true);
        echo "\"
                  data-content=\"";
        // line 84
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("These are all the modules you’ve added to your shop, either by buying it from PrestaShop Addons, or by uploading it directly.", array(), "Admin.Modules.Help"), "html", null, true);
        echo "\">
                </span>
            </div>
            ";
        // line 87
        $this->loadTemplate("PrestaShopBundle:Admin/Module/Includes:grid_manage_installed.html.twig", "PrestaShopBundle:Admin/Module:manage.html.twig", 87)->display(array_merge($context, array("modules" => $this->getAttribute((isset($context["modules"]) ? $context["modules"] : $this->getContext($context, "modules")), "modules", array()), "display_type" => "list", "origin" => "manage", "id" => "all")));
        // line 88
        echo "
            <hr class=\"top-menu-separator\">

            <div class=\"module-short-list\">
                <span class=\"module-search-result-wording\">";
        // line 92
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("%nbModules% built-in modules", array("%nbModules%" => twig_length_filter($this->env, $this->getAttribute((isset($context["modules"]) ? $context["modules"] : $this->getContext($context, "modules")), "native_modules", array()))), "Admin.Modules.Feature"), "html", null, true);
        echo "</span>
                <span class=\"help-box\" data-toggle=\"popover\"
                  data-title=\"";
        // line 94
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Built-in modules", array(), "Admin.Modules.Feature"), "html", null, true);
        echo "\"
                  data-content=\"";
        // line 95
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("These modules from PrestaShop are preinstalled when you set-up your shop. They cover the basics of e-commerce and comes for free.", array(), "Admin.Modules.Help"), "html", null, true);
        echo "\">
                </span>
            </div>
            ";
        // line 98
        $this->loadTemplate("PrestaShopBundle:Admin/Module/Includes:grid_manage_installed.html.twig", "PrestaShopBundle:Admin/Module:manage.html.twig", 98)->display(array_merge($context, array("modules" => $this->getAttribute((isset($context["modules"]) ? $context["modules"] : $this->getContext($context, "modules")), "native_modules", array()), "display_type" => "list", "origin" => "manage", "id" => "native")));
        // line 99
        echo "
            <hr class=\"top-menu-separator\">

            <div class=\"module-short-list\">
                <span class=\"module-search-result-wording\">";
        // line 103
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("%nbModules% theme modules", array("%nbModules%" => twig_length_filter($this->env, $this->getAttribute((isset($context["modules"]) ? $context["modules"] : $this->getContext($context, "modules")), "theme_bundle", array()))), "Admin.Modules.Feature"), "html", null, true);
        echo "</span>
                <span class=\"help-box\" data-toggle=\"popover\"
                  data-title=\"";
        // line 105
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Theme modules", array(), "Admin.Modules.Feature"), "html", null, true);
        echo "\"
                  data-content=\"";
        // line 106
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Each theme you install will come with its own set of modules. You’ll find here all the modules related to your active theme.", array(), "Admin.Modules.Help"), "html", null, true);
        echo "\">
                </span>
            </div>
            ";
        // line 109
        $this->loadTemplate("PrestaShopBundle:Admin/Module/Includes:grid_manage_installed.html.twig", "PrestaShopBundle:Admin/Module:manage.html.twig", 109)->display(array_merge($context, array("modules" => $this->getAttribute((isset($context["modules"]) ? $context["modules"] : $this->getContext($context, "modules")), "theme_bundle", array()), "display_type" => "list", "origin" => "manage", "id" => "theme")));
        // line 110
        echo "
        </div>
    </div>
";
        
        $__internal_9707fe0e0a78883a9db5428acc8ea108b9fca633763b8ad9c370597ec5f7707c->leave($__internal_9707fe0e0a78883a9db5428acc8ea108b9fca633763b8ad9c370597ec5f7707c_prof);

    }

    public function getTemplateName()
    {
        return "PrestaShopBundle:Admin/Module:manage.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  254 => 110,  252 => 109,  246 => 106,  242 => 105,  237 => 103,  231 => 99,  229 => 98,  223 => 95,  219 => 94,  214 => 92,  208 => 88,  206 => 87,  200 => 84,  196 => 83,  191 => 81,  188 => 80,  178 => 72,  174 => 71,  170 => 70,  166 => 68,  159 => 64,  155 => 63,  151 => 62,  147 => 61,  143 => 60,  139 => 59,  135 => 58,  131 => 56,  129 => 55,  124 => 52,  121 => 51,  119 => 50,  116 => 49,  114 => 48,  111 => 47,  109 => 46,  106 => 45,  102 => 42,  96 => 41,  87 => 38,  83 => 37,  79 => 36,  75 => 35,  70 => 34,  64 => 33,  55 => 30,  51 => 29,  46 => 28,  40 => 27,  33 => 25,  31 => 119,  30 => 115,  11 => 25,);
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
{% extends 'PrestaShopBundle:Admin:layout.html.twig' %}

{% block stylesheets %}
    {{ parent() }}
    <link rel=\"stylesheet\" href=\"{{ asset('themes/default/css/bundle/module/drop.css') }}\" />
    <link rel=\"stylesheet\" href=\"{{ asset('themes/default/css/bundle/plugins/jquery.pstagger.css') }}\" />
{% endblock %}

{% block javascripts %}
    {{ parent() }}
    <script src=\"{{ asset('themes/default/js/bundle/plugins/jquery.pstagger.js') }}\"></script>
    <script src=\"{{ asset('themes/default/js/bundle/module/loader.js') }}\"></script>
    <script src=\"{{ asset('themes/default/js/bundle/module/module_card.js') }}\"></script>
    <script src=\"{{ asset('themes/default/js/bundle/module/module.js') }}\"></script>
{% endblock %}

{% block content %}
    <div class=\"row\">
        <div class=\"col-lg-10 col-lg-offset-1\">
            {# Bulk Action confirm modal #}
            {% include 'PrestaShopBundle:Admin/Module/Includes:modal_confirm_bulk_action.html.twig' %}
            {# Addons connect modal with level authorization#}
            {% include 'PrestaShopBundle:Admin/Module/Includes:modal_addons_connect.html.twig' with { 'level' : level, 'errorMessage' : errorMessage } %}
            {# Contains toolbar-nav for module page with level authorization #}
            {% include 'PrestaShopBundle:Admin/Module/Includes:modal_import.html.twig' with { 'level' : level, 'errorMessage' : errorMessage } %}
            {# Contains menu with Selection/Categories/Popular and Tag Search #}
            {% include 'PrestaShopBundle:Admin/Module/Includes:menu_top.html.twig' with { 'topMenuData': topMenuData } %}
            <div class=\"module-sorting-menu\">
                <div class=\"row\">
                    <div class=\"col-lg-12\">
                        {% if level > constant('PrestaShopBundle\\\\Security\\\\Voter\\\\PageVoter::LEVEL_UPDATE') %}
                            <div class=\"module-sorting module-bulk-actions pull-right\">
                                <select data-toggle=\"select2\" class=\"sort-component hidden-xs-up\">
                                  <option value=\"\" disabled selected>{{ 'Bulk Actions'|trans({}, 'Admin.Global') }}</option>
                                  <option value=\"bulk-uninstall\">{{ 'Uninstall'|trans({}, 'Admin.Actions') }}</option>
                                  <option value=\"bulk-disable\">{{ 'Disable'|trans({}, 'Admin.Actions') }}</option>
                                  <option value=\"bulk-enable\">{{ 'Enable'|trans({}, 'Admin.Actions') }}</option>
                                  <option value=\"bulk-reset\">{{ 'Reset'|trans({}, 'Admin.Actions') }}</option>
                                  <option value=\"bulk-enable-mobile\">{{ 'Enable Mobile'|trans({}, 'Admin.Modules.Feature') }}</option>
                                  <option value=\"bulk-disable-mobile\">{{ 'Disable Mobile'|trans({}, 'Admin.Modules.Feature') }}</option>
                                </select>
                            </div>
                        {% endif %}
                        <div class=\"module-sorting module-sorting-author pull-right\">
                            <select data-toggle=\"select2\" class=\"sort-component hidden-xs-up\">
                              <option value=\"\" disabled>- {{ 'Sort by'|trans({}, 'Admin.Actions') }} -</option>
                              <option value=\"name\">{{ 'Name'|trans({}, 'Admin.Global') }}</option>
                              <option value=\"access-desc\" selected>{{ 'Last access'|trans({}, 'Admin.Modules.Feature') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {# Actual Page Content #}
            <div class=\"module-short-list\">
                <span class=\"module-search-result-wording\">{{ '%nbModules% installed modules'|trans({'%nbModules%' : modules.modules|length}, 'Admin.Modules.Feature') }}</span>
                <span class=\"help-box\" data-toggle=\"popover\"
                  data-title=\"{{ \"Installed modules\"|trans({}, 'Admin.Modules.Feature') }}\"
                  data-content=\"{{ \"These are all the modules you’ve added to your shop, either by buying it from PrestaShop Addons, or by uploading it directly.\"|trans({}, 'Admin.Modules.Help') }}\">
                </span>
            </div>
            {% include 'PrestaShopBundle:Admin/Module/Includes:grid_manage_installed.html.twig' with { 'modules': modules.modules, 'display_type': 'list', 'origin': 'manage', 'id': 'all' } %}

            <hr class=\"top-menu-separator\">

            <div class=\"module-short-list\">
                <span class=\"module-search-result-wording\">{{ '%nbModules% built-in modules'|trans({'%nbModules%' : modules.native_modules|length}, 'Admin.Modules.Feature') }}</span>
                <span class=\"help-box\" data-toggle=\"popover\"
                  data-title=\"{{ \"Built-in modules\"|trans({}, 'Admin.Modules.Feature') }}\"
                  data-content=\"{{ \"These modules from PrestaShop are preinstalled when you set-up your shop. They cover the basics of e-commerce and comes for free.\"|trans({}, 'Admin.Modules.Help') }}\">
                </span>
            </div>
            {% include 'PrestaShopBundle:Admin/Module/Includes:grid_manage_installed.html.twig' with { 'modules': modules.native_modules, 'display_type': 'list', 'origin': 'manage', 'id': 'native' } %}

            <hr class=\"top-menu-separator\">

            <div class=\"module-short-list\">
                <span class=\"module-search-result-wording\">{{ '%nbModules% theme modules'|trans({'%nbModules%' : modules.theme_bundle|length}, 'Admin.Modules.Feature') }}</span>
                <span class=\"help-box\" data-toggle=\"popover\"
                  data-title=\"{{ \"Theme modules\"|trans({}, 'Admin.Modules.Feature') }}\"
                  data-content=\"{{ \"Each theme you install will come with its own set of modules. You’ll find here all the modules related to your active theme.\"|trans({}, 'Admin.Modules.Help') }}\">
                </span>
            </div>
            {% include 'PrestaShopBundle:Admin/Module/Includes:grid_manage_installed.html.twig' with { 'modules': modules.theme_bundle, 'display_type': 'list', 'origin': 'manage', 'id': 'theme' } %}

        </div>
    </div>
{% endblock %}

{% set js_translatable = {
\"Bulk Action - One module minimum\": \"You need to select at least one module to use the bulk action.\"|trans({}, 'Admin.Modules.Notification'),
\"Bulk Action - Request not found\": 'The action \"[1]\" is not available, impossible to perform your request.'|trans({}, 'Admin.Modules.Notification'),
\"Bulk Action - Request not available for module\": \"The action [1] is not available for module [2]. Skipped.\"|trans({}, 'Admin.Modules.Notification'),
}|merge(js_translatable) %}
", "PrestaShopBundle:Admin/Module:manage.html.twig", "C:\\wamp64\\www\\RogerMaireLocal\\src\\PrestaShopBundle/Resources/views/Admin/Module/manage.html.twig");
    }
}
