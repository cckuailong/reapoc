<?php
/**
 * @author John Hargrove
 * 
 * Date: Jun 6, 2010
 * Time: 5:48:35 PM
 */


require_once WPAM_BASE_DIRECTORY . "/source/Validation/IValidator.php";

class WPAM_Pages_Admin_MyCreativesPage extends WPAM_Pages_Admin_AdminPage
{
	private $response;

	public function processRequest($request)
	{
                if(is_array($request)){
                    $request = wpam_sanitize_array($request);
                }
		$db = new WPAM_Data_DataAccess();

		if(!empty($request['action'])) //changed from isset to resolve strange bug with siliconeapp theme
		{
			if ($request['action'] === 'viewDetail')
			{
				return $this->doDetailView($request);
			}
			else if ($request['action'] === 'new')
			{
				return $this->doNew($request);
			}
			else if ($request['action'] === 'edit')
			{
				return $this->doEdit($request);
			}
		}		
		else
		{
			$response = new WPAM_Pages_TemplateResponse('admin/manage_creatives');

			if(isset($request['statusFilter']))
			{				
				if ($request['statusFilter'] === 'active')
				{
					$response->viewData['creatives'] = $db->getCreativesRepository()->loadAllActiveNoDeletes();
				}
				else if ($request['statusFilter'] === 'inactive')
				{
					$response->viewData['creatives'] = $db->getCreativesRepository()->loadAllInactiveNoDeletes();
				}
			}
			else
			{
				$response->viewData['creatives'] = $db->getCreativesRepository()->loadAllNoDeletes();
			}

			$response->viewData['request'] = $request;
			$response->viewData['statusFilters'] = array(
				'all' => __( 'All', 'affiliates-manager' ),
				'active' => __( 'Active', 'affiliates-manager' ),
				'inactive' => __( 'Inactive', 'affiliates-manager' ),
			);
			return $response;
		}
	}

	protected function getCreativeUpdateForm($request = array(), $validationResult = null)
	{
		//add widget_form_error js to creative_update_form
		add_action('admin_footer', array( $this, 'onFooter' ) );

		$response = new WPAM_Pages_TemplateResponse('admin/creative_update_form');

		$db = new WPAM_Data_DataAccess();
		$images = $db->getWordPressRepository()->getAllImageAttachments();

		$response->viewData['creativeTypes'] = array(
			'none' => "",
			'image' => "Image",
			'text' => 'Text Link'
		);

		$response->viewData['images'] = array(
			'' => ""
		);

		foreach ($images as $image)
		{
			$response->viewData['images'][$image->ID] = "{$image->post_title} ({$image->post_name})";
		}


		$response->viewData['validationResult'] = $validationResult;
		$response->viewData['request'] = $request;

		//save for form validation in the footer
		$this->response = $response;

		return $response;
	}

	protected function doEdit($request)
	{
		if (isset($request['post']) && $request['post'])
			return $this->doCreativeSubmit($request);
		
		$db = new WPAM_Data_DataAccess();
		$creative = $db->getCreativesRepository()->load($request['creativeId']);
		if ($creative === NULL)
			wp_die( __( 'Invalid creative.', 'affiliates-manager' ) );
		
		// load up the request, show the form
		$request['txtName'] = $creative->name;
		$request['txtSlug'] = $creative->slug;
		$request['ddType'] = $creative->type;

		if ($creative->type === 'image')
		{
			$request['txtImageAltText'] = $creative->altText;
			$request['ddFileImage'] = $creative->imagePostId;
                        $request['image_url'] = isset($creative->image) && !empty($creative->image) ? $creative->image : '';
		}
		else if ($creative->type === 'text')
		{
			$request['txtLinkText'] = $creative->linkText;
			$request['txtAltText'] = $creative->altText;
		}

		return $this->getCreativeUpdateForm($request);
	}

	protected function doNew($request)
	{
		if (isset($request['post']) && $request['post'])
		{
			return $this->doCreativeSubmit($request);
		}
		return $this->getCreativeUpdateForm($request);
	}

	protected function doCreativeSubmit($request)
	{
                $nonce = $request['_wpnonce'];
                if(!wp_verify_nonce($nonce, 'wpam_save_creatives_nonce')){
                    wp_die(__('Error! Nonce Security Check Failed! Go back to the My Creatives menu and add a creative again.', 'affiliates-manager'));
                }
		$validator = new WPAM_Validation_Validator();				
		$db = new WPAM_Data_DataAccess();
		$image_url = '';
		$validator->addValidator('txtName', new WPAM_Validation_StringValidator(1));
		//$validator->addValidator('ddLandingPage', new WPAM_Validation_SetValidator(array('index','products')));
		$validator->addValidator('ddType', new WPAM_Validation_SetValidator(array('image','text')));

		if ($request['ddType'] === 'image')
		{
                    if(isset($request['image_url']) && !empty($request['image_url'])){
                        $image_url = $request['image_url'];
                    }
                    $validator->addValidator('image_url', new WPAM_Validation_StringValidator(1));
		}
		else if ($request['ddType'] === 'text')
		{
                    $validator->addValidator('txtLinkText', new WPAM_Validation_StringValidator(1));
		}

		$vr = $validator->validate($request);

		if ($vr->getIsValid())
		{
			$creativesRepo = $db->getCreativesRepository();

			if( $request['action'] === 'edit' ) {
				$model = $creativesRepo->load( $request['creativeId'] );
			} else {
				$model = new WPAM_Data_Models_CreativeModel();
				$model->dateCreated = time();
				//#50 new creatives start as 'inactive'
				$model->status = 'active';
			}
			
			$model->type = $request['ddType'];
			if ($model->type === 'image')
			{
                            if(isset($request['ddFileImage']) && !empty($request['ddFileImage'])){
				$model->imagePostId = $request['ddFileImage'];
                            }
                            $model->altText = $request['txtImageAltText'];
                            $model->image = $image_url;
			}
			else if ($model->type === 'text')
			{
				$model->linkText = $request['txtLinkText'];
				$model->altText = $request['txtAltText'];
			}
			else
			{
				wp_die( __( 'Insert failed: Bad creative type.', 'affiliates-manager' ) );
			}
			$model->slug = $request['txtSlug'];
			$model->name = $request['txtName'];
			
			$db = new WPAM_Data_DataAccess();
			$response = new WPAM_Pages_TemplateResponse('admin/creatives_detail');
			if ($request['action'] === 'edit')
			{
				$response->viewData['updateMessage'] = __( 'Creative Updated.', 'affiliates-manager' );
				$creativesRepo->update($model);
			}
			else if ($request['action'] === 'new')
			{
				$id = $creativesRepo->insert($model);
				$model->creativeId = $id;
				$response->viewData['updateMessage'] = __( 'Creative ... created.', 'affiliates-manager' );
			}
			else
			{
				wp_die( __( 'Insert failed: invalid creative update mechanism.', 'affiliates-manager' ) );
			}
			
			$response->viewData['request'] = $request;
			$response->viewData['creative'] = $model;
			
			return $response;
		}
		else
		{
			return $this->getCreativeUpdateForm($request, $vr);
		}
	}



	protected function doDetailView($request)
	{
		if (!is_numeric($request['creativeId']))
			wp_die( __('Invalid creative.', 'affiliates-manager' ) );

		$creativeId = (int)$request['creativeId'];
		$db = new WPAM_Data_DataAccess();
		$model = $db->getCreativesRepository()->load($creativeId);

		if ($model === NULL)
			wp_die( __('Invalid creative.', 'affiliates-manager' ) );

		$response = new WPAM_Pages_TemplateResponse('admin/creatives_detail');
		$response->viewData['creative'] = $model;
		$response->viewData['request'] = $request;
		return $response;
	}

	public function onFooter() {
		$response = new WPAM_Pages_TemplateResponse('widget_form_errors', $this->response->viewData);
		echo $response->render();
	}
	
}
