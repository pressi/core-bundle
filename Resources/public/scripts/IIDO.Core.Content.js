var IIDO = IIDO || {};
IIDO.Core = IIDO.Core || {};
IIDO.Core.Content = IIDO.Core.Content || {};

(function (window, $, content)
{
    content.init = () =>
    {

    };

})(window, jQuery, IIDO.Core.Content);

document.addEventListener('DOMContentLoaded', () =>
{
    // IIDO.Core.Content.init();

    if( $('.jarallax').length )
    {
        $('.jarallax').jarallax(
        {
            disableParallax: function ()
            {
                // if( /iPad/.test(navigator.userAgent) )
                // {
                //     if( window.innerWidth <= 1024 )
                //     {
                //         return true;
                //     }
                // }

                return /iPhone|iPod|Android/.test(navigator.userAgent);
            }
        });
    }

    if( $('[data-aos]').length )
    {
        setTimeout(() => { AOS.init({duration:2000}); }, 500);
    }
});
