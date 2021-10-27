<?php


namespace IIDO\CoreBundle\Widget;


use Contao\Backend;
use Contao\Controller;
use Contao\Environment;
use Contao\Input;
use Contao\InputUnit;
use Contao\System;


class InputUnitWidget extends InputUnit
{
    public function generate(): string
    {
        $buffer = parent::generate();

        if( $this->name !== 'headline' )
        {
            return $buffer;
        }

        if( Input::get('table') !== 'tl_content' )
        {
            return $buffer;
        }

        $config = System::getContainer()->get('iido.core.config');

//        if( $config->get('extendedHeadlines') )
//        {

            //        $buffer = \preg_replace('/<input([A-Za-z0-9\s\-="\[\]\(\),;.:_]+)value="([A-Za-z0-9\s\-\[\]\(\),;.:_#<>="]{0,})"([A-Za-z0-9\s\-="\[\]\(\),;.:_]{0,})> <select/', '<textarea$1$3>$2</textarea> <select', $buffer);
            $buffer = \preg_replace('/<input/', '<div', $buffer);
            $buffer = \preg_replace('/ <select/', $this->varValue['value'] . '</div> <select', $buffer);
//            $buffer = \preg_replace('/tl_text_unit" value="([A-Za-z0-9\s\-=";,.:#\[\]\(\)&!?ßöäüÖÄÜ\/<>]{0,})" maxlength="/', 'tl_text_unit tl_textarea_unit" maxlength="', $buffer);
        $buffer = \preg_replace('/tl_text_unit" value="([A-Za-z0-9\s\-=";,.:#\[\]\(\)&!?ßöäüÖÄÜ%\/<>\|üb–áàãâúùûóòõôíìîéèê]{0,})" onfocus="/', 'tl_text_unit tl_textarea_unit" onfocus="', $buffer);
            $buffer = \preg_replace('/id="ctrl_headline"/', 'id="headline[value]"', $buffer);
            //echo "\n";
//        echo "<pre>";
//                    echo "<br>";
//            print_r( $buffer );
//            exit;


            $buffer .= '<script>window.tinymce || document.write(\'<script src="' . Controller::replaceInsertTags('{{asset::js/tinymce.min.js::contao-components/tinymce4}}') . '">\x3C/script>\')</script>
<script>
window.tinymce && tinymce.init({
    selector: "div.tl_textarea_unit",
    min_height: 30,
    width: "79%",
    inline: true,
//    plugins: "autosave charmap code fullscreen image importcss link lists paste searchreplace stripnbsp tabfocus table visualblocks visualchars",
    plugins: "code",
//    hidden_input: false,
    menubar: false,
//    toolbar: "undo redo | bold italic light | alignleft aligncenter alignright",
//    toolbar: "undo redo | bold italic | alignleft aligncenter alignright | fontselect fontsizeselect | code",
    toolbar: "undo redo | bold italic | alignleft aligncenter alignright | code",
    forced_root_block : "div",
    statusbar: false,
    language: "' .  Backend::getTinyMceLanguage() . '",
    element_format: "html",
    document_base_url: "' . Environment::get('base') . '",
    entities: "160,nbsp,60,lt,62,gt,173,shy",
    merge_siblings: true,
    branding: false,
    importcss_append: true,
    paste_as_text: true,
//     font_formats: "Benton Sans=BentonSans,sans-serif; Benton Sans Book=BentonSans-Book,sans-serif; Oriflame=Oriflame Script,serif",
//     fontsize_formats: "15px 20px 25px 30px 35px 40px 45px 50px",
//     content_style: "body { font-family: BentonSans,sans-serif; font-size:20px; }",
    setup: function(editor)
    {
        editor.getElement().removeAttribute("required");
    },
    init_instance_callback: function(editor)
    {
        if (document.activeElement && document.activeElement.id && document.activeElement.id == editor.id)
        {
            editor.editorManager.get(editor.id).focus();
        }
        editor.on("focus", function() { Backend.getScrollOffset(); });
    },
//    formats:
//    {
//        light: { inline: "span", attributes: { class: "text-light" }, exact: true }
//    },
//    setup: function( editor )
//    {
//        editor.ui.registry.addButton("light", {
//            text: "L",
//            onAction: function()
//            {
//                editor.insertContent(\'<span class="text-light">\' + editor.selection.getContent() + \'</span>\')
//            }
//        });
//    }
});
</script>';
//        }

        return $buffer;
    }
}
