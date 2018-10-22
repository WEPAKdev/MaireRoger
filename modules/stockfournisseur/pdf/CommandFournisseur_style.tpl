{**
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
 *}

{assign var=color_header value="#F0F0F0"}
{assign var=color_border value="#000000"}
{assign var=color_border_lighter value="#CCCCCC"}
{assign var=color_line_even value="#FFFFFF"}
{assign var=color_line_odd value="#F9F9F9"}
{assign var=font_size_text value="7pt"}
{assign var=font_size_header value="9pt"}
{assign var=font_size_product value="9pt"}
{assign var=height_header value="20px"}
{assign var=table_padding value="4px"}

<style>
    table, th, td {
            margin: 0!important;
            /*padding: 0!important;*/
            vertical-align: middle!important;
            font-size: {$font_size_text};
            white-space: nowrap;
    }

    table.body{
        width:100%;
    }

    th.header{
        background-color:{$color_header};
        padding:{$table_padding}!important;
        border-bottom:1px solid {$color_border};
    }
    
    th.sizehead{
        border-right: solid 1px {$color_border};
    }

    table.body td{
        border-bottom: solid 1px {$color_border};
    }

    .border-right{
         border-right: solid 1px {$color_border};
    }
    
    .border-bottom{
        border-bottom: solid 1px {$color_border};
    }

    tbody tr:first-child td{
        border-top: solid 1px {$color_border};
    }
    
    tr.color_line_even {
        background-color: {$color_line_even};
    }

    tr.color_line_odd {
        background-color: {$color_line_odd};
    }
    
    .color{
        padding-left:2px!important;
    }

    .left {
        text-align: left;
    }

    .fright {
        float: right;
    }

    .right {
        text-align: right;
    }

    .center {
        text-align: center;
    }

    .bold {
        font-weight: bold;
    }

    .border {
        border: 1px solid black;
    }
    .grey {
        background-color: {$color_header};
    }

    /* This is used for the border size */
    .white {
        background-color: #FFFFFF;
    }
    .small {
            font-size:small;
    }
</style>
