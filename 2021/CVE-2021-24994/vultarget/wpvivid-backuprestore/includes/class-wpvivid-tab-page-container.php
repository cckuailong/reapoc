<?php
if (!defined('WPVIVID_PLUGIN_DIR'))
{
    die;
}
class WPvivid_Tab_Page_Container
{
    public $tabs;
    public $container_id;
    public $is_parent_tab=1;

    public function __construct( $args = array() )
    {
        $this->tabs=array();
        $this->container_id=uniqid('tab-');
    }

    public function add_tab($title,$slug,$callback,$args=array())
    {
        $new_tab['title']=$title;
        $new_tab['slug']=$slug;
        $new_tab['page']=$callback;
        foreach ($args as $key=>$arg)
        {
            $new_tab[$key]=$arg;
            if($key === 'is_parent_tab') {
                $this->is_parent_tab = $arg;
            }
        }

        $this->tabs[]=$new_tab;
    }

    public function set_tab($tabs)
    {
        foreach ($tabs as $tab)
        {
            $new_tab['title']=$tab['title'];
            $new_tab['slug']=$tab['slug'];
            $new_tab['page']=$tab['page'];
            $this->tabs[]=$new_tab;
        }
    }

    public function display()
    {
        $class = '';
        ?>
        <div id="<?php echo $this->container_id?>">
            <h2 class="nav-tab-wrapper <?php esc_attr_e($class); ?>" style="padding-bottom:0!important;">
                <?php
                $this->display_tabs();
                ?>
            </h2>
            <?php
            if($this->is_parent_tab){
                ?>
                <div style="margin: 10px 0 0 2px;">
                    <div id="poststuff" style="padding-top: 0;">
                        <div id="post-body" class="metabox-holder columns-2">
                            <div id="post-body-content">
                                <div class="inside" style="margin-top:0;">
                                    <div>
                                        <?php
                                        $this->display_page();
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div id="postbox-container-1" class="postbox-container">
                                <div class="meta-box-sortables">
                                    <?php
                                    if(has_filter('wpvivid_add_side_bar')){
                                        $side_bar = '1';
                                    }
                                    else{
                                        $side_bar = '0';
                                    }
                                    //$side_bar = '';
                                    $side_bar = apply_filters('wpvivid_add_side_bar', $side_bar, false);
                                    echo $side_bar;
                                    ?>
                                </div>
                            </div>
                        </div>
                        <br class="clear">
                    </div>
                </div>
                <?php
            }
            else{
                ?>
                <div>
                    <?php
                    $this->display_page();
                    ?>
                </div>
                <?php
            }
            ?>
        </div>
        <script>
            jQuery('#<?php echo $this->container_id?>').on("click",".<?php echo $this->container_id?>-tab",function()
            {
                jQuery('#<?php echo $this->container_id?>').find( '.<?php echo $this->container_id?>-tab' ).each(function()
                {
                    jQuery(this).removeClass( "nav-tab-active" );
                });

                jQuery('#<?php echo $this->container_id?>').find( '.<?php echo $this->container_id?>-content' ).each(function()
                {
                    jQuery(this).hide();
                });

                var id=jQuery(this).attr('id');
                id= id.substr(12);

                jQuery("#wpvivid_page_"+id).show();
                jQuery(this).addClass( "nav-tab-active" );
            });

            jQuery('#<?php echo $this->container_id?>').on("click",".nav-tab-delete-img",function(event)
            {
                event.stopPropagation();
                var redirect=jQuery(this).attr('redirect');
                jQuery(this).parent().hide();

                jQuery('#<?php echo $this->container_id?>').find( '.<?php echo $this->container_id?>-tab' ).each(function()
                {
                    jQuery(this).removeClass( "nav-tab-active" );
                });

                jQuery('#<?php echo $this->container_id?>').find( '.<?php echo $this->container_id?>-content' ).each(function()
                {
                    jQuery(this).hide();
                });

                jQuery("#wpvivid_page_"+redirect).show();
                jQuery("#wpvivid_tab_"+redirect).addClass( "nav-tab-active" );
                //jQuery(this).addClass( "nav-tab-active" );
            });

            jQuery(document).ready(function($)
            {
                jQuery(document).on('<?php echo $this->container_id?>-show', function(event,id,redirect)
                {
                    jQuery('#<?php echo $this->container_id?>').find( '.<?php echo $this->container_id?>-tab' ).each(function()
                    {
                        jQuery(this).removeClass( "nav-tab-active" );
                    });

                    jQuery('#<?php echo $this->container_id?>').find( '.<?php echo $this->container_id?>-content' ).each(function()
                    {
                        jQuery(this).hide();
                    });

                    jQuery("#wpvivid_page_"+id).show();
                    jQuery("#wpvivid_tab_"+id).show();
                    jQuery("#wpvivid_tab_"+id).find( '.nav-tab-delete-img' ).each(function()
                    {
                        jQuery(this).attr('redirect',redirect);
                    });
                    jQuery("#wpvivid_tab_"+id).addClass( "nav-tab-active" );
                    var top = jQuery("#wpvivid_tab_"+id).offset().top-jQuery("#wpvivid_tab_"+id).height();
                    jQuery('html, body').animate({scrollTop:top}, 'slow');
                });
            });
        </script>
        <?php
    }

    public function display_tabs()
    {
        $first=true;

        foreach ($this->tabs as $tab)
        {
            $class='nav-tab '.$this->container_id.'-tab';
            if($first)
            {
                $class.=' nav-tab-active';
                $first=false;
            }

            $style='cursor:pointer;';

            if(isset($tab['hide']))
            {
                $style.=' display: none';
            }

            if(isset($tab['can_delete']))
            {
                $class.=' delete';
            }
            if(isset($tab['transparency']))
            {
                $class.=' wpvivid-transparency-tab';
            }

            echo '<a id="wpvivid_tab_'.$tab['slug'].'" class="'.$class.'" style="'.$style.'">';

            if(isset($tab['can_delete']))
            {
                echo '<div style="margin-right: 15px;">'.__($tab['title'], 'wpvivid-backuprestore').'</div>';
                if(isset($tab['redirect']))
                {
                    echo '<div class="nav-tab-delete-img" redirect="'.$tab['redirect'].'">
                          <img src="'.esc_url( WPVIVID_PLUGIN_URL.'/admin/partials/images/delete-tab.png' ).'" style="vertical-align:middle; cursor:pointer;">
                       </div>';
                }
                else
                {
                    echo '<div class="nav-tab-delete-img">
                          <img src="'.esc_url( WPVIVID_PLUGIN_URL.'/admin/partials/images/delete-tab.png' ).'" style="vertical-align:middle; cursor:pointer;">
                       </div>';
                }
            }
            else
            {
                echo __($tab['title'], 'wpvivid-backuprestore');
            }
            echo '</a>';
        }
    }

    public function display_page()
    {
        $first=true;
        foreach ($this->tabs as $tab)
        {
            //delete
            $style='display: none;';
            if($first)
            {
                if(isset($tab['hide']))
                {

                }
                else
                {
                    $style='';
                    $first=false;
                }
            }

            $class=$this->container_id.'-content';

            echo '<div id="wpvivid_page_'.$tab['slug'].'" class="'.$class.'" style="'.$style.'">';
            call_user_func($tab['page']);
            echo '</div>';
        }
    }
}