<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTTP;
  use ClicShopping\OM\CLICSHOPPING;

  class ht_google_logo
  {
    public string $code;
    public string $group;
    public string $title;
    public string $description;
    public ?int $sort_order = 0;
    public bool $enabled = false;

    public function __construct()
    {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_header_tags_google_logo_meta_title');
      $this->description = CLICSHOPPING::getDef('module_header_tags_google_logo_meta_description');

      if (\defined('MODULE_HEADER_TAGS_GOOGLE_LOGO_META_STATUS')) {
        $this->sort_order = MODULE_HEADER_TAGS_GOOGLE_LOGO_META_SORT_ORDER;
        $this->enabled = (MODULE_HEADER_TAGS_GOOGLE_LOGO_META_STATUS == 'True');
      }
    }

    public function execute()
    {

      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Db = Registry::get('Db');

      if (!$CLICSHOPPING_Customer->isLoggedOn()) {

        if (MODULE_HEADER_TAGS_GOOGLE_LOGO_META_BANNER_GROUP != '') {

          $banners_group = MODULE_HEADER_TAGS_GOOGLE_LOGO_META_BANNER_GROUP;

          $Qbanner = $CLICSHOPPING_Db->prepare('select banners_image,
                                                 banners_group
                                          from :table_banners
                                          where banners_group = :banners_group
                                          limit 1
                                        ');
          $Qbanner->bindValue(':banners_group', $banners_group);
          $Qbanner->execute();

          $banner = $Qbanner->fetch();

          $footer_tag = '<!--  google logo start -->' . "\n";
          $footer_tag .= '<script type="application/ld+json"> ';
          $footer_tag .= '{ ';
          $footer_tag .= '"@context": "https://schema.org", ';
          $footer_tag .= '"@type": "Organization", ';
          $footer_tag .= '"url": "' . HTTP::typeUrlDomain() . '", ';
          $footer_tag .= '"logo": "' . HTTP::getShopUrlDomain() . $CLICSHOPPING_Template->getDirectoryTemplateImages() . $banner['banners_image'] . '" ';
          $footer_tag .= '} ';
          $footer_tag .= '</script>' . "\n";

          $footer_tag .= '<!-- google logo end -->' . "\n";

          $CLICSHOPPING_Template->addBlock($footer_tag, 'footer_scripts');
        }
      }

    }

    public function isEnabled()
    {
      return $this->enabled;
    }

    public function check()
    {
      return \defined('MODULE_HEADER_TAGS_GOOGLE_LOGO_META_STATUS');
    }

    public function install()
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want to install this module ?',
          'configuration_key' => 'MODULE_HEADER_TAGS_GOOGLE_LOGO_META_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want to install this module ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Veuillez indiquer le groupe de la banni&egrave;re contenant le logo',
          'configuration_key' => 'MODULE_HEADER_TAGS_GOOGLE_LOGO_META_BANNER_GROUP',
          'configuration_value' => '',
          'configuration_description' => 'Veuillez vous référer a&grave; au menu banni&egrave;re pour connaitre le groupe du logo',
          'configuration_group_id' => '6',
          'sort_order' => '2',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort Order',
          'configuration_key' => 'MODULE_HEADER_TAGS_GOOGLE_LOGO_META_SORT_ORDER',
          'configuration_value' => '100',
          'configuration_description' => 'Sort order. Lowest is displayed in first',
          'configuration_group_id' => '6',
          'sort_order' => '80',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );
    }

    public function remove()
    {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    public function keys()
    {
      return array('MODULE_HEADER_TAGS_GOOGLE_LOGO_META_STATUS',
        'MODULE_HEADER_TAGS_GOOGLE_LOGO_META_BANNER_GROUP',
        'MODULE_HEADER_TAGS_GOOGLE_LOGO_META_SORT_ORDER'
      );
    }
  }
