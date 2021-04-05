<?php


namespace IIDO\CoreBundle\Entity;


use Contao\FilesModel;
use Contao\StringUtil;
use IIDO\CoreBundle\Repository\ThemeDesignerRepository;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass=ThemeDesignerRepository::class)
 * @ORM\Table(name="tl_iido_theme_designer")
 */
class ThemeDesignerEntity
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", length=10, options={"unsigned": true})
     */
    private $id;


    /**
     * @ORM\Column(type="integer", length=10, options={"unsigned": true, "default": 0})
     */
    private $tstamp = 0;


    /**
     * @ORM\Column(type="integer", length=10, options={"unsigned": true, "default": 0})
     */
    private $page = 0;


    /**
     * @ORM\Column(name="top_disabled", type="string", length=1, options={"fixed": true, "default": ""})
     */
    private $topDisabled = '';


    /**
     * @ORM\Column(name="top_enable_padding", type="string", length=1, options={"fixed": true, "default": ""});
     */
    private $topEnablePadding = '';


    /**
     * @ORM\Column(name="top_padding", type="string", length=4, options={"default": "1"});
     */
    private $topPadding = 1;


    /**
     * @ORM\Column(name="top_fullwidth", type="string", length=1, options={"fixed": true, "default": ""});
     */
    private $topFullwidth = '';


    /**
     * @ORM\Column(name="top_enable_border", type="string", length=1, options={"fixed": true, "default": ""});
     */
    private $topEnableBorder = '';


    /**
     * @ORM\Column(name="top_border_color", type="string", length=30, options={"fixed": true, "default": ""});
     */
    private $topBorderColor;


    /**
     * @ORM\Column(name="top_enable_shadow", type="string", length=1, options={"fixed": true, "default": ""})
     */
    private $topEnableShadow = '';


    /**
     * @ORM\Column(name="top_shadow", type="string", length=4, options={"default": "1"});
     */
    private $topShadow = 1;


    /**
     * @ORM\Column(name="top_enable_phone", type="string", length=1, options={"fixed": true, "default": ""})
     */
    private $topEnablePhone = '';


    /**
     * @ORM\Column(name="top_enable_email", type="string", length=1, options={"fixed": true, "default": ""})
     */
    private $topEnableEmail = '';


    /**
     * @ORM\Column(name="top_enable_navmeta", type="string", length=1, options={"fixed": true, "default": ""})
     */
    private $topEnableNavmeta = '';


    /**
     * @ORM\Column(name="top_enable_login", type="string", length=1, options={"fixed": true, "default": ""})
     */
    private $topEnableLogin = '';


    /**
     * @ORM\Column(name="top_enable_socials", type="string", length=1, options={"fixed": true, "default": ""})
     */
    private $topEnableSocials = '';


    /**
     * @ORM\Column(name="top_enable_canvastrigger", type="string", length=1, options={"fixed": true, "default": ""})
     */
    private $topEnableCanvastrigger = '';


    /**
     * @ORM\Column(name="top_enable_langswitcher", type="string", length=1, options={"fixed": true, "default": ""})
     */
    private $topEnableLangswitcher = '';


    /**
     * @ORM\Column(name="header_enable_layout", type="string", length=1, options={"fixed": true, "default": ""})
     */
    private $headerEnableLayout = '';


    /**
     * @ORM\Column(name="header_layout", length=32, options={"default": ""})
     */
    private $headerLayout = '';


    /**
     * @ORM\Column(name="header_disabled", type="string", length=1, options={"fixed": true, "default": ""})
     */
    private $headerDisabled = '';


    /**
     * @ORM\Column(name="header_enable_search", type="string", length=1, options={"fixed": true, "default": ""})
     */
    private $headerEnableSearch = '';


    /**
     * @ORM\Column(name="header_enable_langswitcher", type="string", length=1, options={"fixed": true, "default": ""})
     */
    private $headerEnableLangswitcher = '';


    /**
     * @ORM\Column(name="header_enable_socials", type="string", length=1, options={"fixed": true, "default": ""})
     */
    private $headerEnableSocials = '';


    /**
     * @ORM\Column(name="stickyHeader_disabled", type="string", length=1, options={"fixed": true, "default": ""})
     */
    private $stickyHeaderDisabled = '';


    /**
     * @ORM\Column(name="footer_enable_columns", type="string", length=1, options={"fixed": true, "default": ""})
     */
    private $footerEnableColumns = '';


    /**
     * @ORM\Column(name="footer_columns", length=32, options={"default": ""})
     */
    private $footerColumns = '';


    /**
     * @ORM\Column(name="footer_disabled", type="string", length=1, options={"fixed": true, "default": ""})
     */
    private $footerDisabled = '';


    /**
     * @ORM\Column(name="footer_enable_padding", type="string", length=1, options={"fixed": true, "default": ""});
     */
    private $footerEnablePadding = '';


    /**
     * @ORM\Column(name="footer_padding", type="string", length=4, options={"default": "1"});
     */
    private $footerPadding = 1;


    /**
     * @ORM\Column(name="footer_enable_border", type="string", length=1, options={"fixed": true, "default": ""});
     */
    private $footerEnableBorder = '';


    /**
     * @ORM\Column(name="footer_border_color", type="string", length=30, options={"fixed": true, "default": ""});
     */
    private $footerBorderColor;


    /**
     * @ORM\Column(name="bottom_disabled", type="string", length=1, options={"fixed": true, "default": ""})
     */
    private $bottomDisabled = '';


    /**
     * @ORM\Column(name="bottom_center", type="string", length=1, options={"fixed": true, "default": ""})
     */
    private $bottomCenter = '';


    /**
     * @ORM\Column(name="bottom_enable_padding", type="string", length=1, options={"fixed": true, "default": ""});
     */
    private $bottomEnablePadding = '';


    /**
     * @ORM\Column(name="bottom_padding", type="string", length=4, options={"default": "1"});
     */
    private $bottomPadding = 1;


    /**
     * @ORM\Column(name="bottom_enable_border", type="string", length=1, options={"fixed": true, "default": ""});
     */
    private $bottomEnableBorder = '';


    /**
     * @ORM\Column(name="bottom_border_color", type="string", length=30, options={"fixed": true, "default": ""});
     */
    private $bottomBorderColor;


    /**
     * @ORM\Column(name="enable_boxed_layout", type="string", length=1, options={"fixed": true, "default": ""});
     */
    private $enableBoxedLayout = '';


    /**
     * @ORM\Column(name="boxed_layout", length=32, options={"default": "none"})
     */
    private $boxedLayout = 'none';


    /**
     * @ORM\Column(name="boxed_enable_shadow", type="string", length=1, options={"fixed": true, "default": ""});
     */
    private $boxedEnableShadow = '';


    /**
     * TODO: deaktiviert!!
     * ORM\Column(name="boxed_shadow", type="string", length=4, options={"default": "1"});
     */
    private $boxedShadow = 1;


    /**
     * @ORM\Column(name="enable_boxed_margin_top", type="string", length=1, options={"fixed": true, "default": ""});
     */
    private $enableBoxedMarginTop = '';


    /**
     * @ORM\Column(name="boxed_margin_top", type="string", length=4, options={"default": "1"});
     */
    private $boxedMarginTop = 1;


    /**
     * @ORM\Column(name="enable_boxed_margin_top_negativ", type="string", length=1, options={"fixed": true, "default": ""});
     */
    private $enableBoxedMarginTopNegativ = '';


    /**
     * @ORM\Column(name="boxed_margin_top_negativ", type="string", length=4, options={"default": "1"});
     */
    private $boxedMarginTopNegativ = 1;


    /**
     * @ORM\Column(name="enable_boxed_margin", type="string", length=1, options={"fixed": true, "default": ""});
     */
    private $enableBoxedMargin = '';


    /**
     * @ORM\Column(name="boxed_margin", type="string", length=4, options={"default": "1"});
     */
    private $boxedMargin = 1;


    /**
     * @ORM\Column(name="enable_page_background_color", type="string", length=1, options={"fixed": true, "default": ""});
     */
    private $enablePageBackgroundColor = '';


    /**
     * @ORM\Column(name="page_background_color", type="string", length=30, options={"fixed": true, "default": ""});
     */
    private $pageBackgroundColor;


    /**
     * @ORM\Column(name="enable_page_background_repeat", type="string", length=1, options={"fixed": true, "default": ""});
     */
    private $enablePageBackgroundRepeat = '';


    /**
     * @ORM\Column(name="page_background_repeat", length=32, options={"default": ""})
     */
    private $pageBackgroundRepeat = '';


    /**
     * @ORM\Column(name="enable_page_background_position", type="string", length=1, options={"fixed": true, "default": ""});
     */
    private $enablePageBackgroundPosition = '';


    /**
     * @ORM\Column(name="page_background_position", length=32, options={"default": ""})
     */
    private $pageBackgroundPosition = '';


    /**
     * @ORM\Column(name="enable_page_background_size", type="string", length=1, options={"fixed": true, "default": ""});
     */
    private $enablePageBackgroundSize = '';


    /**
     * @ORM\Column(name="page_background_size", length=64, options={"default": ""})
     */
    private $pageBackgroundSize = '';


    /**
     * @ORM\Column(name="fixed_page_background_attachment", type="string", length=1, options={"fixed": true, "default": ""});
     */
    private $fixedPageBackgroundAttachment = '';


    /**
     * @ORM\Column(name="enable_page_background_image", type="string", length=1, options={"fixed": true, "default": ""});
     */
    private $enablePageBackgroundImage = '';


    /**
     * @ORM\Column(name="page_background_image", type="binary", length=16)
     */
    private $pageBackgroundImage = '';


    /**
     * @ORM\Column(name="enable_page_content_width", type="string", length=1, options={"fixed": true, "default": ""});
     */
    private $enablePageContentWidth = '';


    /**
     * @ORM\Column(name="page_content_width", type="string", length=4, options={"default": "500"});
     */
    private $pageContentWidth = 500;


    /**
     * @ORM\Column(name="enable_page_width", type="string", length=1, options={"fixed": true, "default": ""});
     */
    private $enablePageWidth = '';


    /**
     * @ORM\Column(name="page_width", type="string", length=4, options={"default": "500"});
     */
    private $pageWidth = 500;


    /**
     * @ORM\Column(name="stickyMobileLogo", type="binary", length=16)
     */
    private $stickyMobileLogo = '';


    /**
     * @ORM\Column(name="enableStickyMobileLogo", type="string", length=1, options={"fixed": true, "default": ""})
     */
    private $enableStickyMobileLogo = '';


    /**
     * @ORM\Column(name="mobileLogo", type="binary", length=16)
     */
    private $mobileLogo = '';


    /**
     * @ORM\Column(name="enableMobileLogo", type="string", length=1, options={"fixed": true, "default": ""})
     */
    private $enableMobileLogo = '';


    /**
     * @ORM\Column(name="stickyLogo", type="binary", length=16)
     */
    private $stickyLogo = '';


    /**
     * @ORM\Column(name="enableStickyLogo", type="string", length=1, options={"fixed": true, "default": ""})
     */
    private $enableStickyLogo = '';


    /**
     * @ORM\Column(name="logo", type="binary", length=16)
     * Serializer\Type("string")
     */
    private $logo = '';


    /**
     * @ORM\Column(name="enableLogo", type="string", length=1, options={"fixed": true, "default": ""})
     */
    private $enableLogo = '';



    public function __construct()
    {
        // SET DEFAULTS??
    }



    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }



    /**
     * @param mixed $id
     *
     * @return ThemeDesignerEntity
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }



    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }



    /**
     * @param int $page
     *
     * @return ThemeDesignerEntity
     */
    public function setPage(int $page): ThemeDesignerEntity
    {
        $this->page = $page;
        return $this;
    }



    /**
     * @return int
     */
    public function getTstamp(): int
    {
        return $this->tstamp;
    }



    /**
     * @param int $tstamp
     *
     * @return ThemeDesignerEntity
     */
    public function setTstamp(int $tstamp): ThemeDesignerEntity
    {
        $this->tstamp = $tstamp;
        return $this;
    }



    /**
     * @return string
     */
    public function getTopDisabled(): string
    {
        return $this->topDisabled;
    }



    /**
     * @param string $topDisabled
     *
     * @return ThemeDesignerEntity
     */
    public function setTopDisabled(string $topDisabled): ThemeDesignerEntity
    {
        $this->topDisabled = $topDisabled;
        return $this;
    }



    /**
     * @return string
     */
    public function getTopEnablePhone(): string
    {
        return $this->topEnablePhone;
    }



    /**
     * @param string $topEnablePhone
     *
     * @return ThemeDesignerEntity
     */
    public function setTopEnablePhone(string $topEnablePhone): ThemeDesignerEntity
    {
        $this->topEnablePhone = $topEnablePhone;
        return $this;
    }



    /**
     * @return string
     */
    public function getTopEnableEmail(): string
    {
        return $this->topEnableEmail;
    }



    /**
     * @param string $topEnableEmail
     *
     * @return ThemeDesignerEntity
     */
    public function setTopEnableEmail(string $topEnableEmail): ThemeDesignerEntity
    {
        $this->topEnableEmail = $topEnableEmail;
        return $this;
    }



    /**
     * @return string
     */
    public function getTopEnableNavmeta(): string
    {
        return $this->topEnableNavmeta;
    }



    /**
     * @param string $topEnableNavmeta
     *
     * @return ThemeDesignerEntity
     */
    public function setTopEnableNavmeta(string $topEnableNavmeta): ThemeDesignerEntity
    {
        $this->topEnableNavmeta = $topEnableNavmeta;
        return $this;
    }



    /**
     * @return string
     */
    public function getTopEnableLogin(): string
    {
        return $this->topEnableLogin;
    }



    /**
     * @param string $topEnableLogin
     *
     * @return ThemeDesignerEntity
     */
    public function setTopEnableLogin(string $topEnableLogin): ThemeDesignerEntity
    {
        $this->topEnableLogin = $topEnableLogin;
        return $this;
    }



    /**
     * @return string
     */
    public function getTopEnableSocials(): string
    {
        return $this->topEnableSocials;
    }



    /**
     * @param string $topEnableSocials
     *
     * @return ThemeDesignerEntity
     */
    public function setTopEnableSocials(string $topEnableSocials): ThemeDesignerEntity
    {
        $this->topEnableSocials = $topEnableSocials;
        return $this;
    }



    /**
     * @return string
     */
    public function getTopEnableCanvastrigger(): string
    {
        return $this->topEnableCanvastrigger;
    }



    /**
     * @param string $topEnableCanvastrigger
     *
     * @return ThemeDesignerEntity
     */
    public function setTopEnableCanvastrigger(string $topEnableCanvastrigger): ThemeDesignerEntity
    {
        $this->topEnableCanvastrigger = $topEnableCanvastrigger;
        return $this;
    }



    /**
     * @return string
     */
    public function getTopEnableLangswitcher(): string
    {
        return $this->topEnableLangswitcher;
    }



    /**
     * @param string $topEnableLangswitcher
     *
     * @return ThemeDesignerEntity
     */
    public function setTopEnableLangswitcher(string $topEnableLangswitcher): ThemeDesignerEntity
    {
        $this->topEnableLangswitcher = $topEnableLangswitcher;
        return $this;
    }



    /**
     * @return string
     */
    public function getHeaderLayout(): string
    {
        return $this->headerLayout;
    }



    /**
     * @param string $headerLayout
     *
     * @return ThemeDesignerEntity
     */
    public function setHeaderLayout(string $headerLayout): ThemeDesignerEntity
    {
        $this->headerLayout = $headerLayout;
        return $this;
    }



    /**
     * @return string
     */
    public function getHeaderDisabled(): string
    {
        return $this->headerDisabled;
    }



    /**
     * @param string $headerDisabled
     *
     * @return ThemeDesignerEntity
     */
    public function setHeaderDisabled(string $headerDisabled): ThemeDesignerEntity
    {
        $this->headerDisabled = $headerDisabled;
        return $this;
    }



    /**
     * @return string
     */
    public function getHeaderEnableSearch(): string
    {
        return $this->headerEnableSearch;
    }



    /**
     * @param string $headerEnableSearch
     *
     * @return ThemeDesignerEntity
     */
    public function setHeaderEnableSearch(string $headerEnableSearch): ThemeDesignerEntity
    {
        $this->headerEnableSearch = $headerEnableSearch;
        return $this;
    }



    /**
     * @return string
     */
    public function getHeaderEnableLangswitcher(): string
    {
        return $this->headerEnableLangswitcher;
    }



    /**
     * @param string $headerEnableLangswitcher
     *
     * @return ThemeDesignerEntity
     */
    public function setHeaderEnableLangswitcher(string $headerEnableLangswitcher): ThemeDesignerEntity
    {
        $this->headerEnableLangswitcher = $headerEnableLangswitcher;
        return $this;
    }



    /**
     * @return string
     */
    public function getHeaderEnableSocials(): string
    {
        return $this->headerEnableSocials;
    }



    /**
     * @param string $headerEnableSocials
     *
     * @return ThemeDesignerEntity
     */
    public function setHeaderEnableSocials(string $headerEnableSocials): ThemeDesignerEntity
    {
        $this->headerEnableSocials = $headerEnableSocials;
        return $this;
    }



    /**
     * @return string
     */
    public function getStickyHeaderDisabled(): string
    {
        return $this->stickyHeaderDisabled;
    }



    /**
     * @param string $stickyHeaderDisabled
     *
     * @return ThemeDesignerEntity
     */
    public function setStickyHeaderDisabled(string $stickyHeaderDisabled): ThemeDesignerEntity
    {
        $this->stickyHeaderDisabled = $stickyHeaderDisabled;
        return $this;
    }



    /**
     * @return string
     */
    public function getFooterColumns(): string
    {
        return $this->footerColumns;
    }



    /**
     * @param string $footerColumns
     *
     * @return ThemeDesignerEntity
     */
    public function setFooterColumns(string $footerColumns): ThemeDesignerEntity
    {
        $this->footerColumns = $footerColumns;
        return $this;
    }



    /**
     * @return string
     */
    public function getFooterDisabled(): string
    {
        return $this->footerDisabled;
    }



    /**
     * @param string $footerDisabled
     *
     * @return ThemeDesignerEntity
     */
    public function setFooterDisabled(string $footerDisabled): ThemeDesignerEntity
    {
        $this->footerDisabled = $footerDisabled;
        return $this;
    }



    /**
     * @return string
     */
    public function getBottomDisabled(): string
    {
        return $this->bottomDisabled;
    }



    /**
     * @param string $bottomDisabled
     *
     * @return ThemeDesignerEntity
     */
    public function setBottomDisabled(string $bottomDisabled): ThemeDesignerEntity
    {
        $this->bottomDisabled = $bottomDisabled;
        return $this;
    }



    /**
     * @return string
     */
    public function getBottomCenter(): string
    {
        return $this->bottomCenter;
    }



    /**
     * @param string $bottomCenter
     *
     * @return ThemeDesignerEntity
     */
    public function setBottomCenter(string $bottomCenter): ThemeDesignerEntity
    {
        $this->bottomCenter = $bottomCenter;
        return $this;
    }



    /**
     * @return string
     */
    public function getBoxedLayout(): string
    {
        return $this->boxedLayout;
    }



    /**
     * @param string $boxedLayout
     *
     * @return ThemeDesignerEntity
     */
    public function setBoxedLayout(string $boxedLayout): ThemeDesignerEntity
    {
        $this->boxedLayout = $boxedLayout;
        return $this;
    }



    /**
     * @return string
     */
    public function getStickyMobileLogo(): string
    {
        return $this->stickyMobileLogo;
    }



    /**
     * @param string $stickyMobileLogo
     *
     * @return ThemeDesignerEntity
     */
    public function setStickyMobileLogo(string $stickyMobileLogo): ThemeDesignerEntity
    {
        $this->stickyMobileLogo = $stickyMobileLogo;
        return $this;
    }



    /**
     * @return string
     */
    public function getEnableStickyMobileLogo(): string
    {
        return $this->enableStickyMobileLogo;
    }



    /**
     * @param string $enableStickyMobileLogo
     *
     * @return ThemeDesignerEntity
     */
    public function setEnableStickyMobileLogo(string $enableStickyMobileLogo): ThemeDesignerEntity
    {
        $this->enableStickyMobileLogo = $enableStickyMobileLogo;
        return $this;
    }



    /**
     * @return FilesModel|null
     */
    public function getMobileLogo(): ?FilesModel
    {
        return FilesModel::findByPk( stream_get_contents($this->mobileLogo) );
    }



    /**
     * @param string $mobileLogo
     *
     * @return ThemeDesignerEntity
     */
    public function setMobileLogo(string $mobileLogo): ThemeDesignerEntity
    {
        $this->mobileLogo = $mobileLogo;
        return $this;
    }



    /**
     * @return string
     */
    public function getEnableMobileLogo(): string
    {
        return $this->enableMobileLogo;
    }



    /**
     * @param string $enableMobileLogo
     *
     * @return ThemeDesignerEntity
     */
    public function setEnableMobileLogo(string $enableMobileLogo): ThemeDesignerEntity
    {
        $this->enableMobileLogo = $enableMobileLogo;
        return $this;
    }



    /**
     * @return string
     */
    public function getStickyLogo(): string
    {
        return $this->stickyLogo;
    }



    /**
     * @param string $stickyLogo
     *
     * @return ThemeDesignerEntity
     */
    public function setStickyLogo(string $stickyLogo): ThemeDesignerEntity
    {
        $this->stickyLogo = $stickyLogo;
        return $this;
    }



    /**
     * @return string
     */
    public function getEnableStickyLogo(): string
    {
        return $this->enableStickyLogo;
    }



    /**
     * @param string $enableStickyLogo
     *
     * @return ThemeDesignerEntity
     */
    public function setEnableStickyLogo(string $enableStickyLogo): ThemeDesignerEntity
    {
        $this->enableStickyLogo = $enableStickyLogo;
        return $this;
    }



    /**
     * @return FilesModel|null
     */
    public function getLogo(): ?FilesModel
    {
        return FilesModel::findByPk( stream_get_contents($this->logo) );
    }



    /**
     * @param string $logo
     *
     * @return ThemeDesignerEntity
     */
    public function setLogo(string $logo): ThemeDesignerEntity
    {
        $this->logo = $logo;
        return $this;
    }



    /**
     * @return string
     */
    public function getEnableLogo(): string
    {
        return $this->enableLogo;
    }



    /**
     * @param string $enableLogo
     *
     * @return ThemeDesignerEntity
     */
    public function setEnableLogo(string $enableLogo): ThemeDesignerEntity
    {
        $this->enableLogo = $enableLogo;
        return $this;
    }



    /**
     * @return int
     */
    public function getTopEnablePadding(): string
    {
        return $this->topEnablePadding;
    }



    /**
     * @param string $topEnablePadding
     *
     * @return ThemeDesignerEntity
     */
    public function setTopEnablePadding(string $topEnablePadding): ThemeDesignerEntity
    {
        $this->topEnablePadding = $topEnablePadding;
        return $this;
    }



    /**
     * @return int
     */
    public function getTopPadding(): int
    {
        return $this->topPadding;
    }



    /**
     * @param int $topPadding
     *
     * @return ThemeDesignerEntity
     */
    public function setTopPadding(int $topPadding): ThemeDesignerEntity
    {
        $this->topPadding = $topPadding;
        return $this;
    }



    /**
     * @return string
     */
    public function getTopFullwidth(): string
    {
        return $this->topFullwidth;
    }



    /**
     * @param string $topFullwidth
     *
     * @return ThemeDesignerEntity
     */
    public function setTopFullwidth(string $topFullwidth): ThemeDesignerEntity
    {
        $this->topFullwidth = $topFullwidth;
        return $this;
    }



    /**
     * @return string
     */
    public function getTopEnableBorder(): string
    {
        return $this->topEnableBorder;
    }



    /**
     * @param string $topEnableBorder
     *
     * @return ThemeDesignerEntity
     */
    public function setTopEnableBorder(string $topEnableBorder): ThemeDesignerEntity
    {
        $this->topEnableBorder = $topEnableBorder;
        return $this;
    }



    /**
     * @return mixed
     */
    public function getTopBorderColor()
    {
        return $this->topBorderColor;
    }



    /**
     * @param mixed $topBorderColor
     *
     * @return ThemeDesignerEntity
     */
    public function setTopBorderColor($topBorderColor)
    {
        $this->topBorderColor = $topBorderColor;
        return $this;
    }



    /**
     * @return string
     */
    public function getTopEnableShadow(): string
    {
        return $this->topEnableShadow;
    }



    /**
     * @param string $topEnableShadow
     *
     * @return ThemeDesignerEntity
     */
    public function setTopEnableShadow(string $topEnableShadow): ThemeDesignerEntity
    {
        $this->topEnableShadow = $topEnableShadow;
        return $this;
    }



    /**
     * @return int
     */
    public function getTopShadow(): int
    {
        return $this->topShadow;
    }



    /**
     * @param int $topShadow
     *
     * @return ThemeDesignerEntity
     */
    public function setTopShadow(int $topShadow): ThemeDesignerEntity
    {
        $this->topShadow = $topShadow;
        return $this;
    }



    /**
     * @return string
     */
    public function getFooterEnableColumns(): string
    {
        return $this->footerEnableColumns;
    }



    /**
     * @param string $footerEnableColumns
     *
     * @return ThemeDesignerEntity
     */
    public function setFooterEnableColumns(string $footerEnableColumns): ThemeDesignerEntity
    {
        $this->footerEnableColumns = $footerEnableColumns;
        return $this;
    }



    /**
     * @return string
     */
    public function getFooterEnablePadding(): string
    {
        return $this->footerEnablePadding;
    }



    /**
     * @param string $footerEnablePadding
     *
     * @return ThemeDesignerEntity
     */
    public function setFooterEnablePadding(string $footerEnablePadding): ThemeDesignerEntity
    {
        $this->footerEnablePadding = $footerEnablePadding;
        return $this;
    }



    /**
     * @return int
     */
    public function getFooterPadding(): int
    {
        return $this->footerPadding;
    }



    /**
     * @param int $footerPadding
     *
     * @return ThemeDesignerEntity
     */
    public function setFooterPadding(int $footerPadding): ThemeDesignerEntity
    {
        $this->footerPadding = $footerPadding;
        return $this;
    }



    /**
     * @return string
     */
    public function getFooterEnableBorder(): string
    {
        return $this->footerEnableBorder;
    }



    /**
     * @param string $footerEnableBorder
     *
     * @return ThemeDesignerEntity
     */
    public function setFooterEnableBorder(string $footerEnableBorder): ThemeDesignerEntity
    {
        $this->footerEnableBorder = $footerEnableBorder;
        return $this;
    }



    /**
     * @return mixed
     */
    public function getFooterBorderColor()
    {
        return $this->footerBorderColor;
    }



    /**
     * @param mixed $footerBorderColor
     *
     * @return ThemeDesignerEntity
     */
    public function setFooterBorderColor($footerBorderColor)
    {
        $this->footerBorderColor = $footerBorderColor;
        return $this;
    }



    /**
     * @return string
     */
    public function getHeaderEnableLayout(): string
    {
        return $this->headerEnableLayout;
    }



    /**
     * @param string $headerEnableLayout
     *
     * @return ThemeDesignerEntity
     */
    public function setHeaderEnableLayout(string $headerEnableLayout): ThemeDesignerEntity
    {
        $this->headerEnableLayout = $headerEnableLayout;
        return $this;
    }



    /**
     * @return string
     */
    public function getEnableBoxedLayout(): string
    {
        return $this->enableBoxedLayout;
    }



    /**
     * @param string $enableBoxedLayout
     *
     * @return ThemeDesignerEntity
     */
    public function setEnableBoxedLayout(string $enableBoxedLayout): ThemeDesignerEntity
    {
        $this->enableBoxedLayout = $enableBoxedLayout;
        return $this;
    }



    /**
     * @return string
     */
    public function getBoxedEnableShadow(): string
    {
        return $this->boxedEnableShadow;
    }



    /**
     * @param string $boxedEnableShadow
     *
     * @return ThemeDesignerEntity
     */
    public function setBoxedEnableShadow(string $boxedEnableShadow): ThemeDesignerEntity
    {
        $this->boxedEnableShadow = $boxedEnableShadow;
        return $this;
    }



    /**
     * @return int
     */
    public function getBoxedShadow(): int
    {
        return $this->boxedShadow;
    }



    /**
     * @param int $boxedShadow
     *
     * @return ThemeDesignerEntity
     */
    public function setBoxedShadow(int $boxedShadow): ThemeDesignerEntity
    {
        $this->boxedShadow = $boxedShadow;
        return $this;
    }



    /**
     * @return string
     */
    public function getEnablePageBackgroundColor(): string
    {
        return $this->enablePageBackgroundColor;
    }



    /**
     * @param string $enablePageBackgroundColor
     *
     * @return ThemeDesignerEntity
     */
    public function setEnablePageBackgroundColor(string $enablePageBackgroundColor): ThemeDesignerEntity
    {
        $this->enablePageBackgroundColor = $enablePageBackgroundColor;
        return $this;
    }



    /**
     * @return mixed
     */
    public function getPageBackgroundColor()
    {
        return $this->pageBackgroundColor;
    }



    /**
     * @param mixed $pageBackgroundColor
     *
     * @return ThemeDesignerEntity
     */
    public function setPageBackgroundColor($pageBackgroundColor)
    {
        $this->pageBackgroundColor = $pageBackgroundColor;
        return $this;
    }



    /**
     * @return string
     */
    public function getEnablePageBackgroundRepeat(): string
    {
        return $this->enablePageBackgroundRepeat;
    }



    /**
     * @param string $enablePageBackgroundRepeat
     *
     * @return ThemeDesignerEntity
     */
    public function setEnablePageBackgroundRepeat(string $enablePageBackgroundRepeat): ThemeDesignerEntity
    {
        $this->enablePageBackgroundRepeat = $enablePageBackgroundRepeat;
        return $this;
    }



    /**
     * @return string
     */
    public function getPageBackgroundRepeat(): string
    {
        return $this->pageBackgroundRepeat;
    }



    /**
     * @param string $pageBackgroundRepeat
     *
     * @return ThemeDesignerEntity
     */
    public function setPageBackgroundRepeat(string $pageBackgroundRepeat): ThemeDesignerEntity
    {
        $this->pageBackgroundRepeat = $pageBackgroundRepeat;
        return $this;
    }



    /**
     * @return string
     */
    public function getEnablePageBackgroundPosition(): string
    {
        return $this->enablePageBackgroundPosition;
    }



    /**
     * @param string $enablePageBackgroundPosition
     *
     * @return ThemeDesignerEntity
     */
    public function setEnablePageBackgroundPosition(string $enablePageBackgroundPosition): ThemeDesignerEntity
    {
        $this->enablePageBackgroundPosition = $enablePageBackgroundPosition;
        return $this;
    }



    /**
     * @return string
     */
    public function getPageBackgroundPosition(): string
    {
        return $this->pageBackgroundPosition;
    }



    /**
     * @param string $pageBackgroundPosition
     *
     * @return ThemeDesignerEntity
     */
    public function setPageBackgroundPosition(string $pageBackgroundPosition): ThemeDesignerEntity
    {
        $this->pageBackgroundPosition = $pageBackgroundPosition;
        return $this;
    }



    /**
     * @return string
     */
    public function getEnablePageBackgroundSize(): string
    {
        return $this->enablePageBackgroundSize;
    }



    /**
     * @param string $enablePageBackgroundSize
     *
     * @return ThemeDesignerEntity
     */
    public function setEnablePageBackgroundSize(string $enablePageBackgroundSize): ThemeDesignerEntity
    {
        $this->enablePageBackgroundSize = $enablePageBackgroundSize;
        return $this;
    }



    /**
     * @return array
     */
    public function getPageBackgroundSize(): array
    {
        return json_decode( $this->pageBackgroundSize, true );
    }



    /**
     * @param string $pageBackgroundSize
     *
     * @return ThemeDesignerEntity
     */
    public function setPageBackgroundSize(string $pageBackgroundSize): ThemeDesignerEntity
    {
        $this->pageBackgroundSize = $pageBackgroundSize;
        return $this;
    }



    /**
     * @return string
     */
    public function getFixedPageBackgroundAttachment(): string
    {
        return $this->fixedPageBackgroundAttachment;
    }



    /**
     * @param string $fixedPageBackgroundAttachment
     *
     * @return ThemeDesignerEntity
     */
    public function setFixedPageBackgroundAttachment(string $fixedPageBackgroundAttachment): ThemeDesignerEntity
    {
        $this->fixedPageBackgroundAttachment = $fixedPageBackgroundAttachment;
        return $this;
    }



    /**
     * @return string
     */
    public function getEnablePageBackgroundImage(): string
    {
        return $this->enablePageBackgroundImage;
    }



    /**
     * @param string $enablePageBackgroundImage
     *
     * @return ThemeDesignerEntity
     */
    public function setEnablePageBackgroundImage(string $enablePageBackgroundImage): ThemeDesignerEntity
    {
        $this->enablePageBackgroundImage = $enablePageBackgroundImage;
        return $this;
    }



    /**
     * @return string
     */
    public function getPageBackgroundImage(): ?FilesModel
    {
        return FilesModel::findByPk( stream_get_contents($this->pageBackgroundImage) );
    }



    /**
     * @param string $pageBackgroundImage
     *
     * @return ThemeDesignerEntity
     */
    public function setPageBackgroundImage(string $pageBackgroundImage): ThemeDesignerEntity
    {
        $this->pageBackgroundImage = $pageBackgroundImage;
        return $this;
    }



    /**
     * @return string
     */
    public function getEnablePageContentWidth(): string
    {
        return $this->enablePageContentWidth;
    }



    /**
     * @param string $enablePageContentWidth
     *
     * @return ThemeDesignerEntity
     */
    public function setEnablePageContentWidth(string $enablePageContentWidth): ThemeDesignerEntity
    {
        $this->enablePageContentWidth = $enablePageContentWidth;
        return $this;
    }



    /**
     * @return int
     */
    public function getPageContentWidth(): int
    {
        return $this->pageContentWidth;
    }



    /**
     * @param int $pageContentWidth
     *
     * @return ThemeDesignerEntity
     */
    public function setPageContentWidth(int $pageContentWidth): ThemeDesignerEntity
    {
        $this->pageContentWidth = $pageContentWidth;
        return $this;
    }



    /**
     * @return string
     */
    public function getEnableBoxedMarginTop(): string
    {
        return $this->enableBoxedMarginTop;
    }



    /**
     * @param string $enableBoxedMarginTop
     *
     * @return ThemeDesignerEntity
     */
    public function setEnableBoxedMarginTop(string $enableBoxedMarginTop): ThemeDesignerEntity
    {
        $this->enableBoxedMarginTop = $enableBoxedMarginTop;
        return $this;
    }



    /**
     * @return int
     */
    public function getBoxedMarginTop(): int
    {
        return $this->boxedMarginTop;
    }



    /**
     * @param int $boxedMarginTop
     *
     * @return ThemeDesignerEntity
     */
    public function setBoxedMarginTop(int $boxedMarginTop): ThemeDesignerEntity
    {
        $this->boxedMarginTop = $boxedMarginTop;
        return $this;
    }



    /**
     * @return string
     */
    public function getEnableBoxedMarginTopNegativ(): string
    {
        return $this->enableBoxedMarginTopNegativ;
    }



    /**
     * @param string $enableBoxedMarginTopNegativ
     *
     * @return ThemeDesignerEntity
     */
    public function setEnableBoxedMarginTopNegativ(string $enableBoxedMarginTopNegativ): ThemeDesignerEntity
    {
        $this->enableBoxedMarginTopNegativ = $enableBoxedMarginTopNegativ;
        return $this;
    }



    /**
     * @return int
     */
    public function getBoxedMarginTopNegativ(): int
    {
        return $this->boxedMarginTopNegativ;
    }



    /**
     * @param int $boxedMarginTopNegativ
     *
     * @return ThemeDesignerEntity
     */
    public function setBoxedMarginTopNegativ(int $boxedMarginTopNegativ): ThemeDesignerEntity
    {
        $this->boxedMarginTopNegativ = $boxedMarginTopNegativ;
        return $this;
    }



    /**
     * @return string
     */
    public function getEnableBoxedMargin(): string
    {
        return $this->enableBoxedMargin;
    }



    /**
     * @param string $enableBoxedMargin
     *
     * @return ThemeDesignerEntity
     */
    public function setEnableBoxedMargin(string $enableBoxedMargin): ThemeDesignerEntity
    {
        $this->enableBoxedMargin = $enableBoxedMargin;
        return $this;
    }



    /**
     * @return int
     */
    public function getBoxedMargin(): int
    {
        return $this->boxedMargin;
    }



    /**
     * @param int $boxedMargin
     *
     * @return ThemeDesignerEntity
     */
    public function setBoxedMargin(int $boxedMargin): ThemeDesignerEntity
    {
        $this->boxedMargin = $boxedMargin;
        return $this;
    }



    /**
     * @return string
     */
    public function getEnablePageWidth(): string
    {
        return $this->enablePageWidth;
    }



    /**
     * @param string $enablePageWidth
     *
     * @return ThemeDesignerEntity
     */
    public function setEnablePageWidth(string $enablePageWidth): ThemeDesignerEntity
    {
        $this->enablePageWidth = $enablePageWidth;
        return $this;
    }



    /**
     * @return int
     */
    public function getPageWidth(): int
    {
        return $this->pageWidth;
    }



    /**
     * @param int $pageWidth
     *
     * @return ThemeDesignerEntity
     */
    public function setPageWidth(int $pageWidth): ThemeDesignerEntity
    {
        $this->pageWidth = $pageWidth;
        return $this;
    }



    /**
     * @return string
     */
    public function getBottomEnablePadding(): string
    {
        return $this->bottomEnablePadding;
    }



    /**
     * @param string $bottomEnablePadding
     *
     * @return ThemeDesignerEntity
     */
    public function setBottomEnablePadding(string $bottomEnablePadding): ThemeDesignerEntity
    {
        $this->bottomEnablePadding = $bottomEnablePadding;
        return $this;
    }



    /**
     * @return int
     */
    public function getBottomPadding(): int
    {
        return $this->bottomPadding;
    }



    /**
     * @param int $bottomPadding
     *
     * @return ThemeDesignerEntity
     */
    public function setBottomPadding(int $bottomPadding): ThemeDesignerEntity
    {
        $this->bottomPadding = $bottomPadding;
        return $this;
    }

}
