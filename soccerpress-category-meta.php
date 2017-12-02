<?php
/*
Plugin Name: Category Meta
Description: Add Meta Data For Taxonomy
Author: manzur
Author URI: https://profiles.wordpress.org/manzurahammed
Text Domain: cm
Domain Path: /languages/
Version: 1.0
*/

class Cm {
	public $category_name;
	public $args;
	public $fields;
	public function __construct($args){
		$this->category_name = $args['cat_name'];
		$this->args = $args;
		$this->fields = $this->args['fields'];
		add_action( 'edited_'.$this->category_name, array($this,'save_taxonomy_custom_meta'), 10, 2 );  
		add_action( 'create_'.$this->category_name, array($this,'save_taxonomy_custom_meta'), 10, 2 );
		add_action( $this->category_name.'_edit_form_fields', array($this,'taxonomy_add_new_meta_field'));
		add_action( $this->category_name.'_add_form_fields', array($this,'taxonomy_add_new_meta_field'));
		add_action( 'admin_enqueue_scripts', array($this, 'loadd_script') );
	}
	
	public function loadd_script(){
		wp_enqueue_script( 'cm-admin-script',plugins_url('/js/cm-admin-script.js',__FILE__),array('jquery'),false,false);
	}
	
	public function taxonomy_add_new_meta_field($term){
		$fieldD = '';
		$term_meta = array();
		if(isset($term->term_id)){
			$t_id = $term->term_id;
			$meta_name = $this->category_name.'_'.$t_id;
			$term_meta = get_option($meta_name);
		}
		if(!empty($this->fields) && is_array($this->fields)){
			foreach($this->fields as $field){
				$description = '<p class="description">'.esc_html(isset($field['desc'])?$field['desc']:'').'</p>';
				$value = isset($term_meta[$field["id"]])?$term_meta[$field["id"]]:'';
				$fid = $field["id"];
				$name = "$this->category_name[$fid]";
				switch($field['type']){
					case 'text':
						$fieldD .= '<tr class="form-field">';
							$fieldD .='<div class="form-field">';
								$fieldD .='<th><label>'.$field['label'].'</label></th>';
									$fieldD .='<td>';
										$fieldD .='<input type="text" name="'.$name.'" id="'.$name.'" value ="'.$value.'">';
										$fieldD .= $description;
									$fieldD .='</td>';
							$fieldD .='</div>';
						$fieldD .='</tr>';
						break;
					case 'image':
						$fieldD .='<tr class="form-field">';
							$fieldD .='<div class="form-field">';
								$fieldD .='<th scope="row" valign="top"><label>'.$field['label'].'</label></th>';
								$fieldD .='<td>';
									$fieldD .= $this->image($fid,$name,$value);
									$fieldD .= $description;
								$fieldD .='</td>';
							$fieldD .='</div>';
						$fieldD .='</tr>';
						break;
					case 'select':
						$fieldD .='<tr class="form-field">';
							$fieldD .='<div class="form-field">';
								$fieldD .='<th scope="row" valign="top"><label>'.$field['label'].'</label></th>';
								$fieldD .='<td>';
									$fieldD .= $this->droupdown($name,$field,$value);
									$fieldD .= $description;
								$fieldD .='</td>';
							$fieldD .='</div>';
						$fieldD .='</tr>';
						break;
				}
			}
		}
		echo $fieldD;
	}
	
	public function droupdown($id,$field,$select,$multiple=false){
		$fieldD = '';
		$select = (array)$select;
		$markup ='<select class="postform" name="'.$id.'" id="'.$id.'">';
		if($multiple){
			$markup ='<select  class="postform" name="'.$id.'[]" multiple="multiple" id="'.$id.'">';
		}
		$fieldD .= $markup;
			if(!empty($field['value']) && is_array($field['value'])){
				foreach($field['value'] as $key => $value){
					$selected = in_array($key,$select)?'selected':'';
					$fieldD .='<option '.$selected.' value="'.$key.'">'.$value.'</option>';
				}
			}
		$fieldD .= '</select>';
		return $fieldD;
	}
	
	public function image($id,$field_name,$value,$multiple=false){
		$markup = $image_thumb = '';
		$image_thumb = plugins_url('../images/placeholder.png',__FILE__);
		if( $value ) {
			$image_thumb = wp_get_attachment_thumb_url( $value );
		}
		$markup .= '<img id="' . $id . '_preview" class="image_preview" src="' . $image_thumb . '" /><br/>' . "\n";
		$markup .= '<input id="' . $id . '_button" type="button" data-uploader_title="' .  esc_html__( 'Upload an image' , 'socccerpress' ) . '" data-uploader_button_text="' .  esc_html__( 'Use image' , 'socccerpress' ) . '" class="image_upload_button button" value="'.  esc_html__( 'Upload new image' , 'socccerpress' ) . '" />' . "\n";
		$markup .= '<input id="' . $id . '_delete" type="button" class="image_delete_button button" value="'.  esc_html__( 'Remove image' , 'socccerpress' ) . '" />' . "\n";
		$markup .= '<input id="' . $id . '" class="image_data_field" type="hidden" name="' . $field_name . '" value="' . $value . '"/><br/>' . "\n";
		return $markup;
	}
	
	function save_taxonomy_custom_meta( $term_id ) {
		if ( isset( $_POST[$this->category_name]) ) {
			$t_id = $term_id;
			$meta = $this->category_name.'_'.$t_id;
			$term_meta = get_option( $meta );
			$post_data = $_POST[$this->category_name];
			$cat_keys = array_keys( $post_data );
			foreach ( $cat_keys as $key ) {
				if ( isset ( $post_data[$key] ) ) {
					$term_meta[$key] = $post_data[$key];
				}
			}
			update_option( $meta, $term_meta );
		}
	}
	public static function create_filter(){
		$data = apply_filters('cm_add_meta',array());
		if(!empty($data) && is_array($data)){
			foreach($data as $item){
				new self($item);
			}
		}
	}
}
add_action('init','Cm::create_filter');