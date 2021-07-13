<?php

if(!class_exists('BC_CF7_Bootstrap_4')){
    final class BC_CF7_Bootstrap_4 {

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    	//
    	// private static
    	//
    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        private static $instance = null;

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    	//
    	// public static
    	//
    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public static function get_instance($file = ''){
            if(null !== self::$instance){
                return self::$instance;
            }
            if('' === $file){
                wp_die(__('File doesn&#8217;t exist?'));
            }
            if(!is_file($file)){
                wp_die(sprintf(__('File &#8220;%s&#8221; doesn&#8217;t exist?'), $file));
            }
            self::$instance = new self($file);
            return self::$instance;
    	}

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    	//
    	// private
    	//
    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        private $file = '';

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	private function __clone(){}

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	private function __construct($file = ''){
            $this->file = $file;
            add_action('plugins_loaded', [$this, 'plugins_loaded']);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	private function checkbox($html = '', $tag = null){
            $html = bc_str_get_html($html);
            $type = 'checkbox';
            if(in_array($tag->basetype, ['checkbox', 'radio'])){
                $type = $tag->basetype;
            }
            $inline = $tag->has_option('inline');
			foreach($html->find('.wpcf7-list-item') as $li){
				$li->addClass('custom-control custom-' . $type);
				if($inline){
                    $li->addClass('custom-control-inline');
                }
				$input = $li->find('input', 0);
				$input->addClass('custom-control-input');
				$input->id = $tag->name . '_' . str_replace('-', '_', sanitize_title($input->value));
				$label = $li->find('.wpcf7-list-item-label', 0);
				$label->addClass('custom-control-label');
				$label->for = $input->id;
				$label->tag = 'label';
				$li->innertext = $input->outertext . $label->outertext;
			}
            return $html;
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	private function file($html = '', $tag = null){
            $html = bc_str_get_html($html);
            $wrapper = $html->find('.wpcf7-form-control-wrap', 0);
            $wrapper->addClass('custom-file');
            $input = $wrapper->find('input', 0);
            $input->addClass('custom-file-input');
            if(!isset($input->id)){
                $input->id = $tag->name;
            }
            $multiple = $tag->has_option('multiple');
            if($multiple){
                $input->multiple = 'multiple';
                $input->name = $input->name . '[]';
            }
            $browse = __('Select');
            $label = __('Select Files');
            $input->outertext = $input->outertext . '<label class="custom-file-label" for="' . $input->id . '" data-browse="' . $browse . '">' . $label . '</label>';
            return $html;
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	private function range($html = '', $tag = null){
            $html = bc_str_get_html($html);
            $wrapper = $html->find('.wpcf7-form-control-wrap', 0);
            $range = $wrapper->find('range', 0);
            $range->addClass('form-control-range');
            return $html;
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	private function select($html = '', $tag = null){
            $html = bc_str_get_html($html);
            $wrapper = $html->find('.wpcf7-form-control-wrap', 0);
            $select = $wrapper->find('select', 0);
            $select->addClass('custom-select');
            return $html;
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	private function submit($html = '', $tag = null){
            $html = bc_str_get_html($html);
            $submit = $html->find('input', 0);
            $submit->addClass('btn');
            $submit->outertext = '<span class="bc-submit-wrap d-flex align-items-center">' . $submit->outertext . '</span>';
            return $html;
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	private function text($html = '', $tag = null){
            $html = bc_str_get_html($html);
            $wrapper = $html->find('.wpcf7-form-control-wrap', 0);
            $input = $wrapper->find('input', 0);
            $input->addClass('form-control');
            return $html;
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    	private function textarea($html = '', $tag = null){
            $html = bc_str_get_html($html);
            $wrapper = $html->find('.wpcf7-form-control-wrap', 0);
            $textarea = $wrapper->find('textarea', 0);
            $textarea->addClass('form-control');
            return $html;
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    	//
    	// public
    	//
    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function plugins_loaded(){
            if(!defined('BC_FUNCTIONS')){
        		return;
        	}
            if(!defined('WPCF7_VERSION')){
        		return;
        	}
            add_action('plugins_loaded', [$this, 'plugins_loaded']);
            add_action('wpcf7_init', [$this, 'wpcf7_init']);
            add_action('wpcf7_enqueue_scripts', [$this, 'wpcf7_enqueue_scripts']);
            add_action('wpcf7_enqueue_styles', [$this, 'wpcf7_enqueue_styles']);
            if(!has_filter('wpcf7_autop_or_not', '__return_false')){
                add_filter('wpcf7_autop_or_not', '__return_false');
            }
            add_filter('wpcf7_validate_password', [$this, 'wpcf7_password_validation_filter'], 10, 2);
            add_filter('wpcf7_validate_password*', [$this, 'wpcf7_password_validation_filter'], 10, 2);
            add_filter('wpcf7_validate_radio*', 'wpcf7_checkbox_validation_filter', 10, 2);
            remove_action('wpcf7_init', 'wpcf7_add_form_tag_acceptance');
    		remove_action('wpcf7_init', 'wpcf7_add_form_tag_checkbox');
    		remove_action('wpcf7_init', 'wpcf7_add_form_tag_date');
    		remove_action('wpcf7_init', 'wpcf7_add_form_tag_file');
    		remove_action('wpcf7_init', 'wpcf7_add_form_tag_number');
    		remove_action('wpcf7_init', 'wpcf7_add_form_tag_select');
            remove_action('wpcf7_init', 'wpcf7_add_form_tag_submit');
    		remove_action('wpcf7_init', 'wpcf7_add_form_tag_text');
    		remove_action('wpcf7_init', 'wpcf7_add_form_tag_textarea');
            bc_build_update_checker('https://github.com/beavercoffee/bc-cf7-bootstrap-4', $this->file, 'bc-cf7-bootstrap-4');
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function wpcf7_init(){
            wpcf7_add_form_tag('acceptance', function($tag){
                $html = wpcf7_acceptance_form_tag_handler($tag);
                return $this->checkbox($html, $tag);
            }, [
        		'name-attr' => true,
			]);
			wpcf7_add_form_tag(['checkbox', 'checkbox*', 'radio', 'radio*'], function($tag){
                $html = wpcf7_checkbox_form_tag_handler($tag);
                return $this->checkbox($html, $tag);
            }, [
				'multiple-controls-container' => true,
        		'name-attr' => true,
                'selectable-values' => true,
        	]);
			wpcf7_add_form_tag(['date', 'date*'], function($tag){
                $html = wpcf7_date_form_tag_handler($tag);
                return $this->text($html, $tag);
            }, [
        		'name-attr' => true,
        	]);
			wpcf7_add_form_tag(['file', 'file*'], function($tag){
                $html = wpcf7_file_form_tag_handler($tag);
                return $this->file($html, $tag);
            }, [
				'file-uploading' => true,
        		'name-attr' => true,
        	]);
			wpcf7_add_form_tag(['number', 'number*'], function($tag){
                $html = wpcf7_number_form_tag_handler($tag);
				return $this->text($html, $tag);
            }, [
        		'name-attr' => true,
        	]);
			wpcf7_add_form_tag(['range', 'range*'], function($tag){
				$html = wpcf7_number_form_tag_handler($tag);
                return $this->range($html, $tag);
            }, [
        		'name-attr' => true,
        	]);
			wpcf7_add_form_tag(['select', 'select*'], function($tag){
                $html = wpcf7_select_form_tag_handler($tag);
                return $this->select($html, $tag);
            }, [
        		'name-attr' => true,
                'selectable-values' => true,
        	]);
            wpcf7_add_form_tag(['submit'], function($tag){
                $html = wpcf7_submit_form_tag_handler($tag);
                return $this->submit($html, $tag);
            });
			wpcf7_add_form_tag(['email', 'email*', 'password', 'password*', 'tel', 'tel*', 'text', 'text*', 'url', 'url*'], function($tag){
                $html = wpcf7_text_form_tag_handler($tag);
                return $this->text($html, $tag);
            }, [
        		'name-attr' => true,
        	]);
            wpcf7_add_form_tag(['textarea', 'textarea*'], function($tag){
                $html = wpcf7_textarea_form_tag_handler($tag);
                return $this->textarea($html, $tag);
            }, [
        		'name-attr' => true,
        	]);
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function wpcf7_enqueue_scripts(){
            wp_enqueue_script('bs-custom-file-input', 'https://cdn.jsdelivr.net/npm/bs-custom-file-input@1.3.4/dist/bs-custom-file-input.min.js', ['contact-form-7'], '1.3.4', true);
            wp_add_inline_script('bs-custom-file-input', 'jQuery(function(){ bsCustomFileInput.init(); });');
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function wpcf7_enqueue_styles(){
            $src = plugin_dir_url($this->file) . 'assets/bc-cf7-bootstrap-4.css';
            $ver = filemtime(plugin_dir_path($this->file) . 'assets/bc-cf7-bootstrap-4.css');
            wp_enqueue_style('bc-cf7-bootstrap-4', $src, ['contact-form-7'], $ver);
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function wpcf7_password_validation_filter($result, $tag){
            $name = $tag->name;
			$value = isset($_POST[$name]) ? trim(wp_unslash(strtr((string) $_POST[$name], "\n", " "))) : '';
			if('password' === $tag->basetype){
				if($tag->is_required() and '' === $value){
					$result->invalidate($tag, wpcf7_get_message('invalid_required'));
				}
			}
			return wpcf7_text_validation_filter($result, $tag);
        }

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    }
}
