<?php

/* ::base.html.twig */
class __TwigTemplate_321dc891bdf3d9d9e94068ca31dac766d7bc04eeead34ccd2ce742b34eb79ab4 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'stylesheets' => array($this, 'block_stylesheets'),
            'body' => array($this, 'block_body'),
            'javascripts' => array($this, 'block_javascripts'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $__internal_7acf8b774cfe071fc1a73942df87494d2c76957bd36d4c517a63703c5df4f502 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_7acf8b774cfe071fc1a73942df87494d2c76957bd36d4c517a63703c5df4f502->enter($__internal_7acf8b774cfe071fc1a73942df87494d2c76957bd36d4c517a63703c5df4f502_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "::base.html.twig"));

        // line 25
        echo "<!DOCTYPE html>
<html>
    <head>
        <meta charset=\"UTF-8\" />
        <title>";
        // line 29
        $this->displayBlock('title', $context, $blocks);
        echo "</title>
        ";
        // line 30
        $this->displayBlock('stylesheets', $context, $blocks);
        // line 31
        echo "        <link rel=\"icon\" type=\"image/x-icon\" href=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("favicon.ico"), "html", null, true);
        echo "\" />
    </head>
    <body>
        ";
        // line 34
        $this->displayBlock('body', $context, $blocks);
        // line 35
        echo "        ";
        $this->displayBlock('javascripts', $context, $blocks);
        // line 36
        echo "    </body>
</html>
";
        
        $__internal_7acf8b774cfe071fc1a73942df87494d2c76957bd36d4c517a63703c5df4f502->leave($__internal_7acf8b774cfe071fc1a73942df87494d2c76957bd36d4c517a63703c5df4f502_prof);

    }

    // line 29
    public function block_title($context, array $blocks = array())
    {
        $__internal_ce6650deaafd2532dfd7e489f749161e1f2a8c8058919519f42302faff954cbb = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_ce6650deaafd2532dfd7e489f749161e1f2a8c8058919519f42302faff954cbb->enter($__internal_ce6650deaafd2532dfd7e489f749161e1f2a8c8058919519f42302faff954cbb_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "title"));

        echo "Welcome!";
        
        $__internal_ce6650deaafd2532dfd7e489f749161e1f2a8c8058919519f42302faff954cbb->leave($__internal_ce6650deaafd2532dfd7e489f749161e1f2a8c8058919519f42302faff954cbb_prof);

    }

    // line 30
    public function block_stylesheets($context, array $blocks = array())
    {
        $__internal_1b7deed71c41a3d4cb99435af00c89e49593707af635046a9a103f29f7c9b045 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_1b7deed71c41a3d4cb99435af00c89e49593707af635046a9a103f29f7c9b045->enter($__internal_1b7deed71c41a3d4cb99435af00c89e49593707af635046a9a103f29f7c9b045_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "stylesheets"));

        
        $__internal_1b7deed71c41a3d4cb99435af00c89e49593707af635046a9a103f29f7c9b045->leave($__internal_1b7deed71c41a3d4cb99435af00c89e49593707af635046a9a103f29f7c9b045_prof);

    }

    // line 34
    public function block_body($context, array $blocks = array())
    {
        $__internal_f45e8ca85e413c10603130e6875a9f18fde530a2c8c01c4383b9e533708031ff = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_f45e8ca85e413c10603130e6875a9f18fde530a2c8c01c4383b9e533708031ff->enter($__internal_f45e8ca85e413c10603130e6875a9f18fde530a2c8c01c4383b9e533708031ff_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "body"));

        
        $__internal_f45e8ca85e413c10603130e6875a9f18fde530a2c8c01c4383b9e533708031ff->leave($__internal_f45e8ca85e413c10603130e6875a9f18fde530a2c8c01c4383b9e533708031ff_prof);

    }

    // line 35
    public function block_javascripts($context, array $blocks = array())
    {
        $__internal_3c5c5d027536ab2ca63c6177553ffbc39ff29475055775164b2e41b4e2895446 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_3c5c5d027536ab2ca63c6177553ffbc39ff29475055775164b2e41b4e2895446->enter($__internal_3c5c5d027536ab2ca63c6177553ffbc39ff29475055775164b2e41b4e2895446_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "javascripts"));

        
        $__internal_3c5c5d027536ab2ca63c6177553ffbc39ff29475055775164b2e41b4e2895446->leave($__internal_3c5c5d027536ab2ca63c6177553ffbc39ff29475055775164b2e41b4e2895446_prof);

    }

    public function getTemplateName()
    {
        return "::base.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  93 => 35,  82 => 34,  71 => 30,  59 => 29,  50 => 36,  47 => 35,  45 => 34,  38 => 31,  36 => 30,  32 => 29,  26 => 25,);
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
<!DOCTYPE html>
<html>
    <head>
        <meta charset=\"UTF-8\" />
        <title>{% block title %}Welcome!{% endblock %}</title>
        {% block stylesheets %}{% endblock %}
        <link rel=\"icon\" type=\"image/x-icon\" href=\"{{ asset('favicon.ico') }}\" />
    </head>
    <body>
        {% block body %}{% endblock %}
        {% block javascripts %}{% endblock %}
    </body>
</html>
", "::base.html.twig", "C:\\wamp64\\www\\RogerMaireLocal\\app/Resources\\views/base.html.twig");
    }
}
