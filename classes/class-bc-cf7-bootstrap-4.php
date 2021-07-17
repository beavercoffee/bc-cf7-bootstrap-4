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
            add_action('bc_cf7_fields_loaded', [$this, 'bc_cf7_fields_loaded']);
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
                $freetext = '';
				if($li->hasClass('has-free-text')){
					$freetext = $li->find('.wpcf7-free-text', 0);
					$freetext->addClass('form-control mt-1');
                    $freetext->value = apply_filters('bc_cf7_free_text_value', (string) $freetext->value, $tag);
				}
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
				$li->innertext = $input->outertext . $label->outertext . $freetext;
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

        public function bc_cf7_fields_loaded(){
            add_action('wpcf7_enqueue_scripts', [$this, 'wpcf7_enqueue_scripts']);
            add_action('wpcf7_enqueue_styles', [$this, 'wpcf7_enqueue_styles']);
            add_filter('bc_cf7_field', [$this, 'bc_cf7_field'], 10, 5);
            if(!has_filter('wpcf7_autop_or_not', '__return_false')){
                add_filter('wpcf7_autop_or_not', '__return_false');
            }
            bc_build_update_checker('https://github.com/beavercoffee/bc-cf7-bootstrap-4', $this->file, 'bc-cf7-bootstrap-4');
            do_action('bc_cf7_bootstrap_4_loaded');
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function bc_cf7_field($html, $tag, $type, $basetype, $original_html){
            switch($type){
                case 'acceptance':
                case 'checkbox':
                case 'radio':
                    $html = $this->checkbox($original_html, $tag);
                    break;
                case 'date':
                case 'email':
                case 'number':
                case 'password':
                case 'tel':
                case 'text':
                case 'url':
                    $html = $this->text($original_html, $tag);
                    break;
                case 'file':
                    $html = $this->file($original_html, $tag);
                    break;
                case 'range':
                    $html = $this->range($original_html, $tag);
                    break;
                case 'select':
                    $html = $this->select($original_html, $tag);
                    break;
                case 'submit':
                    $html = $this->submit($original_html, $tag);
                    break;
                case 'textarea':
                    $html = $this->textarea($original_html, $tag);
                    break;
            }
            return $html;
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

    }
}
