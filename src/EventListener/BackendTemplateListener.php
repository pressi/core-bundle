<?php
/*******************************************************************
 * (c) 2021 Stephan Preßl, www.prestep.at <development@prestep.at>
 * All rights reserved
 * Modification, distribution or any other action on or with
 * this file is permitted unless explicitly granted by IIDO
 * www.iido.at <development@iido.at>
 *******************************************************************/

namespace IIDO\CoreBundle\EventListener;


use Contao\CoreBundle\ServiceAnnotation\Hook;
use IIDO\CoreBundle\Config\BundleConfig;


class BackendTemplateListener
{
    /**
     * @Hook("outputBackendTemplate")
     */
    public function onOutputBackendTemplate( string $content, string $template )
    {
//        $GLOBALS['TL_CSS']['be_iido_styles'] = BundleConfig::getBundlePath( true, false ) . '/styles/backend.scss||static';

        return $content;
    }



    /**
     * @Hook("parseBackendTemplate")
     */
    public function onParseBackendTemplate( string $content, string $template )
    {
        if( "be_welcome" === $template )
        {
            $content = preg_replace('/<div id="([A-Za-z0-9\s\-_]+)">/', '<div id="$1"><div class="inside">', $content, -1, $count);

            if( $count )
            {
                $content = preg_replace('/<\/div>/', '</div></div>', $content);
            }


            preg_match_all('/<p>(.*?)<\/p>/', $content, $matches);

            $introMessage = '<div id="tl_intro"><div class="inside">' . $matches[0][0] . '</div></div>';

            $content = preg_replace('/<p>(.*?)<\/p>/', '', $content, 1);
            $content = str_replace('<div id="tl_messages">', $introMessage . '<div id="tl_messages">', $content);

            $noMessages = '<p>Zurzeit sind keine Hinweise vorhanden.</p>';
            preg_match_all('/<div id="tl_messages">([A-Za-z0-9\s\-<>=",;.:_öäüÖÄÜß!?\/\(\)\{\}]+)<\/div>/', $content, $matches);
            preg_match_all('/<p>(.*?)<\/p>/', $matches[1][0], $matches);

            if( count($matches[0]) <= 1 )
            {
                $content = preg_replace('/<div id="tl_messages"><div class="inside">([\n\s]{0,})<h2>([A-Za-z0-9\s\-,;.:_öäüÖÄÜß!?]+)<\/h2>/', '<div id="tl_messages"><div class="inside">$1<h2>$2</h2>' . $noMessages, $content);
            }
        }

        elseif( "be_main" === $template )
        {
            if( str_contains($content, 'Dashboard') )
            {
                $content = str_replace('class="content"', 'class="content dashboard"', $content);
            }
        }

        return $content;
    }




    /**
     * Edit the Frontend Template
     *
     * @param string $strContent
     * @param string $strTemplate
     *
     * @return string
     */
    public function outputCustomizeBackendTemplate($strContent, $strTemplate)
    {
        $config = \Config::getInstance();

        if( $config->isComplete() )
        {
            if( $strTemplate === "be_main" )
            {
                $strErrorMessage = "";

//				if( $config->get("dps_setDefaultSettings") && $config->get("dps_websiteStatusIsDevelopment") )
//				{
//					$strErrorMessage	.= '<p class="tl_error tl_permalert">' . (($this->User->isAdmin) ? '<a href="' . $this->addToUrl('websiteIsLive=1') . '" class="tl_submit">' . $GLOBALS['TL_LANG']['MSC']['websiteToLive'] . '</a>' : '') . $GLOBALS['TL_LANG']['MSC']['websiteInDevelopment'] . '</p>';
//				}

//				if( !in_array("multicolumnwizard", \ModuleLoader::getActive()) )
//				{
//					$strErrorMessage	.= '<p class="tl_info tl_permalert">' . (($this->User->isAdmin) ? '<a href="' . $this->addToUrl('do=composer&amp;install=menatwork/contao-multicolumnwizard') . '" class="tl_submit">Module installieren</a>' : '') . 'Bitte installieren Sie das Module "multicolumnwizard" um alle Funktionen verwenden zu können!</p>';
//				}

//				if( strlen($strErrorMessage) )
//				{
//					$strContent			= preg_replace('/<\/div>([\s\n]{0,})<\/div>([\s\n]{0,})<div id="container"/', $strErrorMessage . '</div></div><div id="container"', $strContent);
//				}
            }

            $scripts = '<script src="assets/jquery/js/jquery.min.js"></script>
<script>jQuery = jQuery.noConflict();</script>
<script src="bundles/iidobasic/javascript/jquery/jquery.hc-sticky.min.js"></script>
<script>setTimeout(function(){var fromTop = jQuery("#header").height(); jQuery("#left").hcSticky({top:fromTop});jQuery("#container").css("padding-top", fromTop);}, 500);</script>';


//            $strContent = preg_replace('/<\/body>/', $scripts . '</body>', $strContent);
        }

        return $strContent;
    }



    /**
     * Parse the Frontend Template
     *
     * @param string $strContent
     * @param string $strTemplate
     *
     * @return string
     */
    public function parseCustomizeBackendTemplate($strContent, $strTemplate)
    {
        if( $strTemplate === "be_welcome" )
        {
            $strFieldPrefix = BundleConfig::getTableFieldPrefix();

            if( \Config::get( $strFieldPrefix . 'enableSupportForm') )
            {
                $this->checkSupportForm();

                $strAddress = '';
                $strContact = '';

                if( \Config::get( $strFieldPrefix . 'supportCompany') )
                {
                    $strAddress .= '<strong>' . \Config::get( $strFieldPrefix . 'supportCompany') . '</strong><br>';
                }

                if( \Config::get( $strFieldPrefix . 'supportEmployee') )
                {
                    $strAddress .= \Config::get( $strFieldPrefix . 'supportEmployee') . '<br>';
                }

                if( \Config::get( $strFieldPrefix . 'supportStreet') )
                {
                    $strAddress .= \Config::get( $strFieldPrefix . 'supportStreet') . '<br>';
                }

                if( \Config::get( $strFieldPrefix . 'supportPostal') )
                {
                    $strAddress .= \Config::get( $strFieldPrefix . 'supportPostal');
                }

                if( \Config::get( $strFieldPrefix . 'supportCity') )
                {
                    $strAddress .= \Config::get( $strFieldPrefix . 'supportCity') . '<br>';
                }


                if( \Config::get( $strFieldPrefix . 'supportMail') )
                {
                    $strContact .= '<a href="mailto:' . \Config::get( $strFieldPrefix . 'supportMail') . '" target="_blank">' . \Config::get( $strFieldPrefix . 'supportMail') . '</a><br>';
                }

                if( \Config::get( $strFieldPrefix . 'supportPhone') )
                {
                    $strContact .= '<a href="tel:' . \Config::get( $strFieldPrefix . 'supportPhone') . '" target="_blank">' . \Config::get( $strFieldPrefix . 'supportPhone') . '</a><br>';
                }

                $strBackend = '<div class="backend-welcome-container">
    <h2>Support kontaktieren</h2>

    <div class="support-form half-column">

        <form action="' . \Environment::get("request") . '" method="post">
            <input type="hidden" name="FORM_SUBMIT" value="iido_welcome_support">
            <input type="hidden" name="REQUEST_TOKEN" value="' . REQUEST_TOKEN . '">

            <div class="form-widget widget">
                <label for="ctrl_support_subject">Betreff</label>
                <input name="subject" type="text" class="tl_text" id="ctrl_support_subject" required>
            </div>

            <div class="form-widget widget">
                <label for="ctrl_support_message">Nachricht</label>
                <textarea name="message" class="tl_textarea" id="ctrl_support_message" required></textarea>
            </div>

            <div class="form-widget widget widget-submit">
                <button type="submit" class="tl_submit">Support kontaktieren</button>
            </div>

        </form>

    </div>
    <div class="support-contact half-column">
         <p>' . $strAddress . '</p>
         <p>' . $strContact . '</p>
    </div>
</div>';

                $strContent = preg_replace('/<div([A-Za-z0-9\s\-,;.:_\/="]{0,})id="tl_versions/', $strBackend . '<div$1id="tl_versions', $strContent);
            }
        }

        return $strContent;
    }



    protected function checkSupportForm()
    {
        if( \Input::post("FORM_SUBMIT") === "iido_welcome_support" )
        {
            $adminEmail = \Config::get("adminEmail");
            $objUser    = \BackendUser::getInstance();
            $objEmail   = new \Email();

            $objEmail->subject  = 'Support Anfrage (' . \Config::get("websiteTitle") . ')';

            $objEmail->fromName = 'Website: ' . \Config::get("websiteTitle");
            $objEmail->from     = $objUser->email;

            $objEmail->html     = 'Betreff: ' . \Input::post("subject") . '<br><br>
            Nachricht:<br>' . \Input::post("message") . '<br><br><br>
            Backend-Benutzer: ' . $objUser->username . ' (' . $objUser->email . ')<br><br><br>
            Gesendet von: ' . \Environment::get("base") . ' am ' . date(\Config::get("dateFormat"), time()) . ' um ' . date(\Config::get("timeFormat"), time()) . ' Uhr';

            if( $adminEmail === "development@prestep.at" || $adminEmail === "mail@stephanpresl.at" )
            {
                $objEmail->sendTo( $adminEmail );
            }
            else
            {
                $objEmail->sendTo( $adminEmail, "development@prestep.at" );
            }
        }
    }

}
