<?php

/* PrestaShopBundle:Admin:macros.html.twig */
class __TwigTemplate_4786a2d2256586c530301e5738555b97de65e56ffafa8203a2d85db571f98656 extends Twig_Template
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
        $__internal_94e911dcfa53a3df7c17d1e96eb7f27d32950757177667575b3ec652862c5159 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_94e911dcfa53a3df7c17d1e96eb7f27d32950757177667575b3ec652862c5159->enter($__internal_94e911dcfa53a3df7c17d1e96eb7f27d32950757177667575b3ec652862c5159_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "PrestaShopBundle:Admin:macros.html.twig"));

        // line 28
        echo "
";
        // line 32
        echo "
";
        
        $__internal_94e911dcfa53a3df7c17d1e96eb7f27d32950757177667575b3ec652862c5159->leave($__internal_94e911dcfa53a3df7c17d1e96eb7f27d32950757177667575b3ec652862c5159_prof);

    }

    // line 25
    public function getform_label_tooltip($__name__ = null, $__tooltip__ = null, $__placement__ = null, ...$__varargs__)
    {
        $context = $this->env->mergeGlobals(array(
            "name" => $__name__,
            "tooltip" => $__tooltip__,
            "placement" => $__placement__,
            "varargs" => $__varargs__,
        ));

        $blocks = array();

        ob_start();
        try {
            $__internal_1e4746ce0b50f6b0194cfdd9651ca1ce8c3cc78a00a5d47f44a22916e2c42b52 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
            $__internal_1e4746ce0b50f6b0194cfdd9651ca1ce8c3cc78a00a5d47f44a22916e2c42b52->enter($__internal_1e4746ce0b50f6b0194cfdd9651ca1ce8c3cc78a00a5d47f44a22916e2c42b52_prof = new Twig_Profiler_Profile($this->getTemplateName(), "macro", "form_label_tooltip"));

            // line 26
            echo "    ";
            echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\FormExtension')->renderer->searchAndRenderBlock((isset($context["name"]) ? $context["name"] : $this->getContext($context, "name")), 'label', array("label_attr" => array("tooltip" => (isset($context["tooltip"]) ? $context["tooltip"] : $this->getContext($context, "tooltip")), "tooltip_placement" => ((array_key_exists("placement", $context)) ? (_twig_default_filter((isset($context["placement"]) ? $context["placement"] : $this->getContext($context, "placement")), "top")) : ("top")))));
            echo "
";
            
            $__internal_1e4746ce0b50f6b0194cfdd9651ca1ce8c3cc78a00a5d47f44a22916e2c42b52->leave($__internal_1e4746ce0b50f6b0194cfdd9651ca1ce8c3cc78a00a5d47f44a22916e2c42b52_prof);

        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        } catch (Throwable $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    // line 29
    public function getcheck($__variable__ = null, ...$__varargs__)
    {
        $context = $this->env->mergeGlobals(array(
            "variable" => $__variable__,
            "varargs" => $__varargs__,
        ));

        $blocks = array();

        ob_start();
        try {
            $__internal_562b022a1e2f4891126b0b10cddb877ac0eb6d3279c5442bf3d615a71d01cf23 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
            $__internal_562b022a1e2f4891126b0b10cddb877ac0eb6d3279c5442bf3d615a71d01cf23->enter($__internal_562b022a1e2f4891126b0b10cddb877ac0eb6d3279c5442bf3d615a71d01cf23_prof = new Twig_Profiler_Profile($this->getTemplateName(), "macro", "check"));

            // line 30
            echo "  ";
            echo twig_escape_filter($this->env, (((array_key_exists("variable", $context) && (twig_length_filter($this->env, (isset($context["variable"]) ? $context["variable"] : $this->getContext($context, "variable"))) > 0))) ? ((isset($context["variable"]) ? $context["variable"] : $this->getContext($context, "variable"))) : (false)), "html", null, true);
            echo "
";
            
            $__internal_562b022a1e2f4891126b0b10cddb877ac0eb6d3279c5442bf3d615a71d01cf23->leave($__internal_562b022a1e2f4891126b0b10cddb877ac0eb6d3279c5442bf3d615a71d01cf23_prof);

        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        } catch (Throwable $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    // line 33
    public function gettooltip($__text__ = null, $__icon__ = null, $__position__ = null, ...$__varargs__)
    {
        $context = $this->env->mergeGlobals(array(
            "text" => $__text__,
            "icon" => $__icon__,
            "position" => $__position__,
            "varargs" => $__varargs__,
        ));

        $blocks = array();

        ob_start();
        try {
            $__internal_230d9b23d28b57a88cbc2b4c2d4b356c9af3a43f103e162c8ca9a52d590735f4 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
            $__internal_230d9b23d28b57a88cbc2b4c2d4b356c9af3a43f103e162c8ca9a52d590735f4->enter($__internal_230d9b23d28b57a88cbc2b4c2d4b356c9af3a43f103e162c8ca9a52d590735f4_prof = new Twig_Profiler_Profile($this->getTemplateName(), "macro", "tooltip"));

            // line 34
            echo "  <span data-toggle=\"tooltip\" class=\"label-tooltip\" data-original-title=\"";
            echo twig_escape_filter($this->env, (isset($context["text"]) ? $context["text"] : $this->getContext($context, "text")), "html", null, true);
            echo "\" data-html=\"true\" data-placement=\"";
            echo twig_escape_filter($this->env, ((array_key_exists("position", $context)) ? (_twig_default_filter((isset($context["position"]) ? $context["position"] : $this->getContext($context, "position")), "top")) : ("top")), "html", null, true);
            echo "\">
    <i class=\"material-icons\">";
            // line 35
            echo twig_escape_filter($this->env, (isset($context["icon"]) ? $context["icon"] : $this->getContext($context, "icon")), "html", null, true);
            echo "</i>
  </span>
";
            
            $__internal_230d9b23d28b57a88cbc2b4c2d4b356c9af3a43f103e162c8ca9a52d590735f4->leave($__internal_230d9b23d28b57a88cbc2b4c2d4b356c9af3a43f103e162c8ca9a52d590735f4_prof);

        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        } catch (Throwable $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    public function getTemplateName()
    {
        return "PrestaShopBundle:Admin:macros.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  131 => 35,  124 => 34,  107 => 33,  86 => 30,  71 => 29,  50 => 26,  33 => 25,  25 => 32,  22 => 28,);
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
{% macro form_label_tooltip(name, tooltip, placement) %}
    {{ form_label(name, null, {'label_attr': {'tooltip': tooltip, 'tooltip_placement': placement|default('top')}}) }}
{% endmacro %}

{% macro check(variable) %}
  {{ variable is defined and variable|length > 0 ? variable : false }}
{% endmacro %}

{% macro tooltip(text, icon, position) %}
  <span data-toggle=\"tooltip\" class=\"label-tooltip\" data-original-title=\"{{ text }}\" data-html=\"true\" data-placement=\"{{ position|default('top') }}\">
    <i class=\"material-icons\">{{ icon }}</i>
  </span>
{% endmacro %}
", "PrestaShopBundle:Admin:macros.html.twig", "C:\\wamp\\www\\RogerMaireLocal\\src\\PrestaShopBundle/Resources/views/Admin/macros.html.twig");
    }
}
