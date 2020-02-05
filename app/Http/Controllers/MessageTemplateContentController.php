<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MessageTemplateContent;
use App\Services\MessageTemplateContentService;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;
use App\Services\RoleCheckingHelper;
use App\Models\RoleAction;
use App\Models\RoleCategorie;
use App\Models\Language;
use App\Models\Country;

class MessageTemplateContentController extends Controller
{
    private $category = 'Translations Content';
    private $actionCreate = 'create';
    private $actionUpdate = 'edit all';
    private $actionRemove = 'remove all';
    private $actionView = 'view all';

    public function create(Request $request) {
        $result['success'] = true;

        try {
            $country = Country::where('language_id', $request->language_id)->first();
        }
        catch (\Exception $exception) {
            $result['success'] = false;
            $result['errors'] = array($exception->getMessage());
            return json_encode($result);
        }

        if (!RoleCheckingHelper::checkIfActionAuthorized($country,
            RoleCategorie::where('name', $this->category)->first(),
            RoleAction::where('name', $this->actionCreate)->first(),
            true)) {
            return (new Response('Action is not authorized', 403));
        }

        $messageTemplateContent = new MessageTemplateContent();
        $messageTemplateContent->message_template_id = $request->message_template_id;
        $messageTemplateContent->language_id = $request->language_id;
        $messageTemplateContent->text = $request->text;

        try {
            MessageTemplateContentService::createMessageTemplateContent($messageTemplateContent);
            $result['message_template_content_id'] = $messageTemplateContent->id;
            return json_encode($result);
        }
        catch (ValidationException $validationException) {
            $result['success'] = false;
            $result['errors'] = $validationException->validator->getMessageBag();
            return json_encode($result);
        }
        catch (\Exception $exception) {
            $result['success'] = false;
            $result['errors'] = array($exception->getMessage());
            return json_encode($result);
        }

    }

    public function update(Request $request) {
        $result['success'] = true;

        if (!isset($request->id)) {
            $result['success'] = false;
            $result['errors'] = array("Parameter 'id' is required");
            return json_encode($result);
        }

        try {

            $messageTemplateContent = MessageTemplateContent::find($request->id);
            if (empty($messageTemplateContent)) {
                $result['success'] = false;
                $result['errors'] = array("Message template content with id $request->id not found");
                return json_encode($result);
            }

            try {
                $country = Country::where('language_id', $messageTemplateContent->language_id)->first();
            }
            catch (\Exception $exception) {
                $result['success'] = false;
                $result['errors'] = array($exception->getMessage());
                return json_encode($result);
            }

            if (!RoleCheckingHelper::checkIfActionAuthorized($country,
                RoleCategorie::where('name', $this->category)->first(),
                RoleAction::where('name', $this->actionUpdate)->first(),
                true)) {
                return (new Response('Action is not authorized', 403));
            }

            $messageTemplateContent->message_template_id = $request->message_template_id;
            $messageTemplateContent->language_id = $request->language_id;
            $messageTemplateContent->text = $request->text;

            MessageTemplateContentService::updateMessageTemplateContent($messageTemplateContent);
            return json_encode($result);
        }
        catch (ValidationException $validationException) {
            $result['success'] = false;
            $result['errors'] = $validationException->validator->getMessageBag();
            return json_encode($result);
        }
        catch (\Exception $exception) {
            $result['success'] = false;
            $result['errors'] = array($exception->getMessage());
            return json_encode($result);
        }
    }

    public function delete(Request $request) {

        $result['success'] = true;

        $messageTemplateContent = MessageTemplateContent::find($request->id);

        try {
            $country = Country::where('language_id', $messageTemplateContent->language_id)->first();
        }
        catch (\Exception $exception) {
            $result['success'] = false;
            $result['errors'] = array($exception->getMessage());
            return json_encode($result);
        }

        if (!RoleCheckingHelper::checkIfActionAuthorized($country,
            RoleCategorie::where('name', $this->category)->first(),
            RoleAction::where('name', $this->actionRemove)->first(),
            true)) {
            return (new Response('Action is not authorized', 403));
        }

        if (empty($messageTemplateContent)) {
            $result['success'] = false;
            $result['errors'] = array("Message template content with id $request->id not found");
            return json_encode($result);
        }
        try {
            //$this->authorize('delete', $scenario);

            $messageTemplateContent->delete();
            return json_encode($result);
        }
        catch (\Exception $exception) {
            $result['success'] = false;
            $result['errors'] = array($exception->getMessage());
            return json_encode($result);
        }
    }

    public function getById(Request $request, $id) {

        $result['success'] = true;

        if (!RoleCheckingHelper::checkIfActionAuthorized(NULL,
            RoleCategorie::where('name', $this->category)->first(),
            RoleAction::where('name', $this->actionView)->first())) {
            return (new Response('Action is not authorized', 403));
        }

        if (empty($id)) {
            $result['success'] = false;
            $result['errors'] = array("Field 'id' is required");
        }

        if ($result['success']) {
            try {
                $messageTemplateContent = MessageTemplateContent::with(['language', 'message_template'])
                    ->where('id', $id);

                $country = Country::where('language_id', $messageTemplateContent->language_id);

                $country = RoleCheckingHelper::filterDataByViewRoles($country,
                    RoleCategorie::where('name', $this->category)->first(),
                    '', true)
                    ->first();

                if (!empty($country)) {
                    $result['message_template_content'] = $messageTemplateContent;
                } else {
                    $result['message_template_content'] = [];
                }
                return json_encode($result);
            } catch (\Exception $exception) {
                $result['success'] = false;
                $result['errors'] = array($exception->getMessage());
                return json_encode($result);
            }
        } else {
            return json_encode($result);
        }
    }

    public function get(Request $request) {

        if (!RoleCheckingHelper::checkIfActionAuthorized(NULL,
            RoleCategorie::where('name', $this->category)->first(),
            RoleAction::where('name', $this->actionView)->first())) {
            return (new Response('Action is not authorized', 403));
        }

        $result['success'] = true;
        $result['errors'] = array();

        if (!isset($request->page)) {
            $result['success'] = false;
            $result['errors'] = array_push($result['errors'], "Field 'page' is required");
        }

        if (!isset($request->pagesize)) {
            $result['success'] = false;
            $result['errors'] = array_push($result['errors'], "Field 'pagesize' is required");
        }

        $messageTemplateId = 0;
        if (isset($request->parent_id)) {
            $messageTemplateId = $request->parent_id;
        }

        if ($result['success'] == true) {
            try {
                $data = MessageTemplateContent::where(function($q) use ($messageTemplateId) {
                    if ($messageTemplateId > 0) {
                        $q->where('message_template_id', $messageTemplateId);
                    }
                })
                    //->skip(($request->page - 1) * $request->pageSize)
                    //->take($request->pagesize)
                    ->with(['language', 'message_template']);
                    //->get();

                $countries = Country::whereIn('language_id', $data->get('language_id'));

                $countries = RoleCheckingHelper::filterDataByViewRoles($countries,
                    RoleCategorie::where('name', $this->category)->first(),
                    '', true);

                $data = $data->whereIn('language_id', $countries->get('language_id'))
                    ->get();

                $result['data'] = $data;
                return json_encode($result);
            }
            catch (\Exception $exception) {
                $result['success'] = false;
                $result['errors'] = array($exception->getMessage());
                return json_encode($result);
            }
        } else {
            return json_encode($result);
        }
    }

}
