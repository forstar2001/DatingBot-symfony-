<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use App\Models\MessageTemplate;
use App\Services\MessageTemplateService;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;
use App\Services\RoleCheckingHelper;
use App\Models\RoleAction;
use App\Models\RoleCategorie;


class MessageTemplateController extends Controller
{
    private $category = 'Translations';
    private $actionCreate = 'create';
    private $actionUpdate = 'edit all';
    private $actionRemove = 'remove all';
    private $actionView = 'view all';

    public function create(Request $request) {
        $result['success'] = true;

        $messageTemplate = new MessageTemplate();
        $messageTemplate->variable_name = $request->variable_name;
        $messageTemplate->template_category_id = $request->template_category_id;

        if (!RoleCheckingHelper::checkIfActionAuthorized($messageTemplate,
            RoleCategorie::where('name', $this->category)->first(),
            RoleAction::where('name', $this->actionCreate)->first())) {
            return (new Response('Action is not authorized', 403));
        }

        try {
            MessageTemplateService::createMessageTemplate($messageTemplate);
            $result['message_template_id'] = $messageTemplate->id;
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

            $messageTemplate = MessageTemplate::find($request->id);
            if (empty($messageTemplate)) {
                $result['success'] = false;
                $result['errors'] = array("Message template with id $request->id not found");
                return json_encode($result);
            }

            if (!RoleCheckingHelper::checkIfActionAuthorized($messageTemplate,
                RoleCategorie::where('name', $this->category)->first(),
                RoleAction::where('name', $this->actionUpdate)->first())) {
                return (new Response('Action is not authorized', 403));
            }

            $messageTemplate->variable_name = $request->variable_name;
            $messageTemplate->template_category_id = $request->template_category_id;


            MessageTemplateService::updateMessageTemplate($messageTemplate);
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

        $messageTemplate = MessageTemplate::find($request->id);

        if (empty($messageTemplate)) {
            $result['success'] = false;
            $result['errors'] = array("Message template with id $request->id not found");
            return json_encode($result);
        }

        if (!RoleCheckingHelper::checkIfActionAuthorized($messageTemplate,
            RoleCategorie::where('name', $this->category)->first(),
            RoleAction::where('name', $this->actionRemove)->first())) {
            return (new Response('Action is not authorized', 403));
        }

        try {
            //$this->authorize('delete', $scenario);

            $messageTemplate->delete();
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
                $messageTemplate = MessageTemplate::with('message_template_contents')
                    ->findOrFail($id);
                $result['message_template'] = $messageTemplate;
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

        $categoryId = null;
        if (isset($request->template_category_id)) {
            $categoryId = $request->template_category_id;
        }


        if ($result['success'] == true) {
            try {
                $data = MessageTemplate::where(function ($q) use ($categoryId) {
                    if ($categoryId !== null) {
                        $categories = explode(',', $categoryId);
                        $q->whereIn('template_category_id', $categories);
                    }
                })
                    ->select('*', 'message_templates.id', 'message_templates.variable_name')
                    ->skip(($request->page - 1) * $request->pagesize)
                    ->take($request->pagesize)
                    ->with(['message_template_contents', 'template_category'])
                    ->join('template_categories', 'message_templates.template_category_id', '=', 'template_categories.id')
                    //->join('message_template_contents', 'message_templates.id', '=', 'message_template_contents.message_template_id')
                    ->orderBy('template_categories.name')
                    ->orderBy('message_templates.variable_name')
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
