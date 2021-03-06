<?php

/* PrestaShopBundle:Admin/Product/Include:form_related_products.html.twig */
class __TwigTemplate_c6b376d1c4ba7b618585e53979e0739064c3e1039ec665c34d30efbf409c7b17 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $__internal_c0c314dabda98516d917da6786cc4b27ca088a90cc0a3f2566189cedafc678a4 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_c0c314dabda98516d917da6786cc4b27ca088a90cc0a3f2566189cedafc678a4->enter($__internal_c0c314dabda98516d917da6786cc4b27ca088a90cc0a3f2566189cedafc678a4_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "PrestaShopBundle:Admin/Product/Include:form_related_products.html.twig"));

        // line 25
        echo "<div id=\"related-content\" class=\"row ";
        echo (((twig_length_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), "vars", array()), "value", array()), "data", array())) == 0)) ? ("hide") : (""));
        echo "\">
  <div class=\"col-md-12\">
    <h2>";
        // line 27
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Related product", array(), "Admin.Catalog.Feature"), "html", null, true);
        echo "</h2>
  </div>
  <div class=\"col-xl-8 col-lg-11\">
    <fieldset class=\"form-group\">
        ";
        // line 31
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\FormExtension')->renderer->searchAndRenderBlock((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), 'widget');
        echo "
    </fieldset>
  </div>
  <div class=\"col-md-1\">
      <fieldset class=\"form-group\">
        <button type=\"button\" class=\"btn btn-invisible btn-block delete p-l-0 p-r-0\" id=\"reset_related_product\"><i class=\"material-icons\">delete</i></button>
      </fieldset>
  </div>
</div>
<div class=\"row\">
  <div class=\"col-md-4\">
    <button type=\"button\" class=\"btn btn-primary-outline sensitive open ";
        // line 42
        echo (((twig_length_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), "vars", array()), "value", array()), "data", array())) > 0)) ? ("hide") : (""));
        echo "\" id=\"add-related-product-button\">
      <i class=\"material-icons\">add_circle</i> ";
        // line 43
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Add a related product", array(), "Admin.Catalog.Feature"), "html", null, true);
        echo "
    </button>
  </div>
</div>
";
        
        $__internal_c0c314dabda98516d917da6786cc4b27ca088a90cc0a3f2566189cedafc678a4->leave($__internal_c0c314dabda98516d917da6786cc4b27ca088a90cc0a3f2566189cedafc678a4_prof);

    }

    public function getTemplateName()
    {
        return "PrestaShopBundle:Admin/Product/Include:form_related_products.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  53 => 43,  49 => 42,  35 => 31,  28 => 27,  22 => 25,);
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
<div id=\"related-content\" class=\"row {{ form.vars.value.data|length == 0 ? 'hide':'' }}\">
  <div class=\"col-md-12\">
    <h2>{{ \"Related product\"|trans({}, 'Admin.Catalog.Feature') }}</h2>
  </div>
  <div class=\"col-xl-8 col-lg-11\">
    <fieldset class=\"form-group\">
        {{ form_widget(form) }}
    </fieldset>
  </div>
  <div class=\"col-md-1\">
      <fieldset class=\"form-group\">
        <button type=\"button\" class=\"btn btn-invisible btn-block delete p-l-0 p-r-0\" id=\"reset_related_product\"><i class=\"material-icons\">delete</i></button>
      </fieldset>
  </div>
</div>
<div class=\"row\">
  <div class=\"col-md-4\">
    <button type=\"button\" class=\"btn btn-primary-outline sensitive open {{ form.vars.value.data|length > 0 ? 'hide':'' }}\" id=\"add-related-product-button\">
      <i class=\"material-icons\">add_circle</i> {{ 'Add a related product'|trans({}, 'Admin.Catalog.Feature') }}
    </button>
  </div>
</div>
", "PrestaShopBundle:Admin/Product/Include:form_related_products.html.twig", "C:\\wamp\\www\\RogerMaireLocal\\src\\PrestaShopBundle/Resources/views/Admin/Product/Include/form_related_products.html.twig");
    }
}
