var IIDO = IIDO || {};
IIDO.ThemeDesigner = {};

(function(window, $, themeDesigner)
{

    themeDesigner.init = function()
    {
        let navItems = $('header .navigation span.clickable');

        if( navItems.length )
        {
            navItems.each( function(index, navItem)
            {
                $(navItem).on('click', function(e)
                {
                    let section = $( e.currentTarget ).attr('data-section');
                    let leftSection = $('#left .nav-section[data-section="' + section + '"]');

                    if( leftSection )
                    {
                        $('header .navigation li.active').removeClass('active');
                        $('#left .nav-section.active').removeClass('active');

                        leftSection.addClass('active');
                        $( e.currentTarget ).parent().addClass('active');

                        let options = leftSection.find('.option.accordion');

                        if( options.length === 1 )
                        {
                            let firstOption = $( options[0] );

                            if( !firstOption.hasClass('active') )
                            {
                                firstOption.find('.toggler').click();
                            }
                        }
                    }
                });
            });
        }

        let accordions = $('.accordion .toggler');

        if( accordions.length )
        {
            accordions.each( function(index, accordion)
            {
                $(accordion).on('click', function(e)
                {
                    $( e.currentTarget ).toggleClass('active');
                    $( e.currentTarget ).parent().toggleClass('active')
                });
            });
        }

        let inputUpdateSave = $('input.update-save');

        if( inputUpdateSave.length )
        {
            inputUpdateSave.each( function(index, inputTag)
            {
                $(inputTag).on('change', function(e)
                {
                    let targetEL = $(e.currentTarget);
                    let fieldValue = targetEL.attr('type') === 'checkbox' ? targetEL.is(':checked') : targetEL.value;

                    if( targetEL.hasClass('save-multiple') )
                    {
                        if( targetEL.hasClass('size-select') )
                        {
                            let targetId = targetEL.attr('id');

                            let option = $('#' + targetId.replace(/(_width_|_height_)/, '_option_') ).val();
                            let width = $('#' + targetId.replace(/(_width_|_height_)/, '_width_') ).val();
                            let height = $('#' + targetId.replace(/(_width_|_height_)/, '_height_') ).val();

                            if( option !== '' || (option === '' && width && height) )
                            {
                                IIDO.ThemeDesigner.updateField( targetEL.attr('name').replace(/\[\'(option|width|height)\'\]/, ''), JSON.stringify({'option':option,'width':width,'height':height} ) );
                            }
                        }
                    }
                    else
                    {
                        $('header .status-badge').addClass('saving').html('saving...');

                        IIDO.ThemeDesigner.manageDependsOn( targetEL.attr('name'), fieldValue );
                        IIDO.ThemeDesigner.updateField( targetEL.attr('name'), fieldValue );
                    }
                });
            });
        }

        let selects = $('select.update-save');

        if( selects.length )
        {
            selects.each( function(index, selectTag)
            {
                $(selectTag).on('change', function(e)
                {
                    let targetEL = $(e.currentTarget);
                    let value = e.currentTarget.value;

                    if( targetEL.hasClass('save-multiple') )
                    {
                        if( targetEL.hasClass('size-select') )
                        {
                            let targetId = targetEL.attr('id');
                            let option = value;
                            let width = $('#' + targetId.replace(/_option_/, '_width_') ).val();
                            let height = $('#' + targetId.replace(/_option_/, '_height_') ).val();

                            if( option !== '' || (option === '' && width && height) )
                            {
                                IIDO.ThemeDesigner.updateField( targetEL.attr('name').replace(/\[\'option\'\]/, ''), JSON.stringify({'option':option,'width':width,'height':height} ) );
                            }
                        }
                    }
                    else
                    {
                        let name = targetEL.attr("name");

                        IIDO.ThemeDesigner.manageDependsOn( name, value, false );
                        IIDO.ThemeDesigner.updateField( name, value );
                    }
                });

            });
        }


        let sizeSelectTag = $('select.size-select-select-tag');

        if( sizeSelectTag.length )
        {
            sizeSelectTag.each( function(index, ssTag)
            {
                $(ssTag).on("change", function(e)
                {
                    if( e.currentTarget.value )
                    {
                        e.currentTarget.parentNode.classList.add('not-editable');
                        $(e.currentTarget.parentNode.parentNode).find("input").prop("disabled", true);
                    }
                    else
                    {
                        e.currentTarget.parentNode.classList.remove('not-editable');
                        $(e.currentTarget.parentNode.parentNode).find("input").prop("disabled", false);
                    }
                });
            })
        }


        let toggler = document.getElementById('toggleThemeDesigner');

        if( toggler )
        {
            toggler.addEventListener('click', function()
            {
                document.body.classList.toggle('hide-theme-designer');
            });
        }


        let filesManager = document.querySelectorAll('button.files-manager');

        if( filesManager.length )
        {
            filesManager.forEach( function( fileManager)
            {
                let modalID = fileManager.getAttribute('data-id');
                let modal = $('#filesManagerModel_' + modalID);

                fileManager.addEventListener('click', function(event)
                {
                    event.preventDefault();

                    let fmID        = fileManager.getAttribute('data-id');

                    let strValue    = fileManager.getAttribute('data-value');
                    let frameUrl    = location.href + 'contao/picker?context=file&extras[fieldType]=radio&extras[filesOnly]=1&extras[extensions]=jpg,jpeg,gif,png,tif,tiff,bmp,svg,svgz,webp&value=' + strValue;

                    $.fancybox.open({
                        src: $('#filesManagerModal_' + fmID),
                        type: 'inline',

                        opts:
                        {
                            // modal: true,

                            beforeShow: function()
                            {
                                let header = $('<div class="simple-modal-header" />');
                                let headline = $('<h1 />');
                                // let closer = $('<div class="closer" />');

                                headline.html( $('#filesManagerModal_' + fmID).attr('data-label') );

                                header.append( headline );
                                // header.append( headline, closer );

                                // closer.on('click', function() { $.fancybox.close(); });


                                let body = $('<div class="simple-modal-body" />');
                                let contents = $('<div class="contents" />');
                                let iframe = $('<iframe name="simple-modal-iframe" width="100%" frameBorder="0" />');

                                iframe.attr('height', (window.innerHeight - 300));
                                // ifarme.attr('src', frameUrl);

                                contents.append( iframe );
                                body.append( contents );


                                let footer = $('<div class="simple-modal-footer" />');
                                let close = $('<a class="btn btn-close" title="Schließen" />');
                                let update = $('<a class="btn primary btn-update btn-disabled" title="Anwenden" />');

                                close.html('Schließen');
                                update.html('Anwenden');

                                close.on('click', function(e) { e.preventDefault(); $.fancybox.close(); return false; });
                                update.on('click', function()
                                {
                                    var frm = window.frames['simple-modal-iframe'],
                                        val = [], ul, inp, field, act, it, i, pickerValue, sIndex;

                                    if (frm === undefined)
                                    {
                                        alert('Could not find the SimpleModal frame');
                                        return;
                                    }

                                    ul = frm.document.getElementById('tl_listing');
                                    // Load the previous values (#1816)
                                    if (pickerValue = ul.get('data-picker-value'))
                                    {
                                        val = JSON.parse(pickerValue);
                                    }
                                    inp = ul.getElementsByTagName('input');
                                    for (i=0; i<inp.length; i++)
                                    {
                                        if (inp[i].id.match(/^(check_all_|reset_)/))
                                        {
                                            continue;
                                        }
                                        // Add currently selected value, otherwise remove (#1816)
                                        sIndex = val.indexOf(inp[i].get('value'));
                                        if (inp[i].checked)
                                        {
                                            if (sIndex == -1)
                                            {
                                                val.push(inp[i].get('value'));
                                            }
                                        }
                                        else if (sIndex != -1)
                                        {
                                            val.splice(sIndex, 1);
                                        }
                                    }

                                    IIDO.ThemeDesigner.updateField( fmID, val[0], 'updateFileManager' );
                                    $.fancybox.close();
                                });

                                footer.append( close, update );

                                $('#filesManagerModal_' + fmID).append(header, body, footer);
                            },

                            afterShow: function()
                            {
                                $('#filesManagerModal_' + fmID).find("iframe").attr("src", frameUrl);
                                // $('#filesManagerModel_' + fmID).attr('href', $('#filesManagerModel_' + fmID).attr('href') + strValue);
                            },

                            afterClose: function()
                            {
                                $('#filesManagerModal_' + fmID).html('');
                            }
                        }
                    });

                    return false;
                });
            });
        }


        let layoutSelectors = $('.field.type-selector > .selector-chooser button');

        if( layoutSelectors.length )
        {
            layoutSelectors.each( function(index, layoutSelector)
            {
                let selectorCont = $(layoutSelector.parentNode.nextElementSibling);
                let buttons = selectorCont.find("button");
                let contClose = selectorCont.find(".closer");

                contClose.on('click', function(e)
                {
                    document.body.classList.remove('open-selector');
                    selectorCont.removeClass('open');
                });

                if( buttons.length )
                {
                    buttons.each( function(index, buttonTag)
                    {
                        $(buttonTag).on('click', function(clickEvent)
                        {
                            let buttonValue = $(clickEvent.currentTarget).attr('data-value');
                            let layoutItem = $(clickEvent.currentTarget.parentNode.parentNode.parentNode);

                            layoutItem.addClass("active");
                            layoutItem.siblings().removeClass("active");

                            document.body.classList.remove('open-selector');

                            selectorCont.removeClass('open');

                            let inputTag = $('#selectorInput_' + selectorCont.attr('data-name') );
                            inputTag.val( buttonValue );

                            inputTag.next().find(".layout-image").html( layoutItem.find("img").clone() );
                            inputTag.next().find(".layout-label").html( '<span>' + layoutItem.find('.layout-label').html() + '</span>' );

                            IIDO.ThemeDesigner.updateField( selectorCont.attr('data-name'), buttonValue );
                        });
                    });
                }

                $(layoutSelector).on('click', function(e)
                {
                    e.preventDefault();

                    document.body.classList.add('open-selector');
                    $( e.currentTarget.parentNode.nextElementSibling ).addClass("open");
                });
            });
        }

        IIDO.ThemeDesigner.manageDependsOn();
    };


    themeDesigner.setStatus = function( mode = '' )
    {
        if( mode === 'not' )
        {
            $('header .status-badge').removeClass('saving').addClass('not').html('not saved');
        }
        else if( mode === 'saving' )
        {
            $('header .status-badge').removeClass('not').addClass('saving').html('saving...');
        }
        else
        {
            $('header .status-badge').removeClass('not');
            $('header .status-badge').removeClass('saving');

            $('header .status-badge').html('saved');

            let themeIframe = document.getElementById('themeDesignerIframe');

            themeIframe.src = themeIframe.src;
        }
    };



    themeDesigner.updateField = function( fieldName, fieldValue, method = 'update' )
    {
        $('header .status-badge').addClass('saving').html('saving...');

        if( !method )
        {
            method = 'update';
        }

        $.ajax({
            url: location.origin + '/_themeDesigner/' + method,
            method: 'POST',
            data: {fieldName: fieldName, fieldValue: fieldValue, page: document.body.getAttribute('data-page-id')}
        })
            .done(function( data )
            {
                if( data.success )
                {
                    $('header .status-badge').removeClass('not');
                    $('header .status-badge').removeClass('saving');

                    $('header .status-badge').html('saved');

                    let themeIframe = document.getElementById('themeDesignerIframe');

                    themeIframe.src = themeIframe.src;

                    if( method === 'updateFileManager' )
                    {
                        $('#filesManager' + fieldName).attr('data-value', data.value);
                    }
                }
                else
                {
                    $('header .status-badge').addClass('not').html('not saved');
                }
            })
            .fail(function( fail )
            {
                $('header .status-badge').addClass('not').html('not saved');
            });
    };



    themeDesigner.manageDependsOn = function()
    {
        let fields = $('.option-item[data-dependson]');

        if( fields.length )
        {
            fields.each( function( index, field )
            {
                let dependsOn = JSON.decode( $(field).attr('data-dependson') );
                let fieldDisplay = 'none';

                let parentField = $('[name="' + dependsOn.parent + '"]');

                if( parentField && parentField.prop('checked') )
                {
                    if( !dependsOn.field && !dependsOn.value )
                    {
                        fieldDisplay = 'block';
                    }
                    else
                    {
                        let selectField = parentField.parent().find('select');
                        let selectValue = selectField.val();

                        if( dependsOn.value === selectValue )
                        {
                            fieldDisplay = 'block';
                        }
                    }
                }

                field.style.display = fieldDisplay;
            });
        }
    };


})(window, jQuery, IIDO.ThemeDesigner);

document.addEventListener('DOMContentLoaded', function(e)
{
    IIDO.ThemeDesigner.init();
});
