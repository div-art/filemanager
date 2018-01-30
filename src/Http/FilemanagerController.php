<?php

namespace Divart\Filemanager\Http;

use App\Http\Controllers\Controller;
use Divart\Filemanager\Facades\Filemanager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\User;
use App\Http\Requests;
use JWTAuth;
use JWTAuthException;

class FilemanagerController extends Controller{

    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
    
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $token = null;
        try {
           if ( !$token = JWTAuth::attempt($credentials)) {
            return response()->json(['invalid_email_or_password'], 422);
           }
        } catch (JWTAuthException $e) {
            return response()->json(['failed_to_create_token'], 500);
        }
        return response()->json(compact('token'));
    }

    public function index(Request $request)
    {
        $data = Filemanager::initFolder();
        $data['user'] = JWTAuth::toUser($request->token);
        return response()->json(['msg' => 'success', 'data' => $data], 200);
    }

    public function sort(Request $request)
    {
        $data = Filemanager::initFolder();
        $data = Filemanager::sort($data, $request->value, $request->type);
        $data['user'] = JWTAuth::toUser($request->token);
        return response()->json(['msg' => 'success', 'data' => $data], 200);
    }

    public function getFolder(Request $request, $folder)
    {
        $data = Filemanager::initFolder($request->folder);
        $data['user'] = JWTAuth::toUser($request->token);
        return response()->json(['msg' => 'success', 'data' => $data], 200);
    }

    public function createFolder($folder, Request $request)
    {
        $data = Filemanager::initFolder(Filemanager::createFolder($folder, $request->name));
        $data['user'] = JWTAuth::toUser($request->token);
        return response()->json(['msg' => 'success', 'data' => $data], 201);
    }

    public function updateFolder($folder, Request $request)
    {
        $data = Filemanager::initFolder(Filemanager::renameFolder($folder, $request->name, $request->newname));
        $data['user'] = JWTAuth::toUser($request->token);
        return response()->json(['msg' => 'success', 'data' => $data], 202);
    }

    public function deleteFolder($folder, Request $request)
    {
        $data = Filemanager::initFolder(Filemanager::deleteFolder($folder, $request->name));
        $data['user'] = JWTAuth::toUser($request->token);
        return response()->json(['msg' => 'success', 'data' => $data], 202);
    }

    public function changelocationFolder($folder, Request $request)
    {
        Filemanager::my_copy_all(Filemanager::pathFilemanager().$request->from, Filemanager::pathFilemanager().$request->to);
        $data = Filemanager::initFolder($folder);
        $data['user'] = JWTAuth::toUser($request->token);
        return response()->json(['msg' => 'success', 'data' => $data], 202);
    }

    public function getFile($file, $folder, Request $request)
    {
        $path = Filemanager::replaceAddress($folder);
        $data = Filemanager::initFolder($folder);
        $data['this_file'] = Filemanager::getFile($path, $file);
        $data['user'] = JWTAuth::toUser($request->token);
        return response()->json(['msg' => 'success', 'data' => $data], 200);
    }

    public function createFile($folder, Request $request)
    {
        $data = Filemanager::initFolder(Filemanager::createFile($folder, $request->filename, $request->data));
        $data['user'] = JWTAuth::toUser($request->token);
        return response()->json(['msg' => 'success', 'data' => $data], 201);
    }

    public function updateFile($folder, Request $request)
    {
        $data = Filemanager::initFolder(Filemanager::renameFile($folder, $request->name, $request->newname));
        $data['user'] = JWTAuth::toUser($request->token);
        return response()->json(['msg' => 'success', 'data' => $data], 202);
    }

    public function uploadFile($folder, Request $request)
    {
        if ($request->file('image')) {
            $file = $request->file('image');
            $path = Filemanager::replaceAddress($folder);
            $file->move(Filemanager::pathFilemanager().$path,$file->getClientOriginalName());
        } else {
            Filemanager::uploadFile($folder, $request->name, $request->data);
        }
        $data = Filemanager::initFolder($folder);
        $data['user'] = JWTAuth::toUser($request->token);

        return response()->json(['msg' => 'success', 'data' => $data], 201);
    }

    public function deleteFile($folder, Request $request)
    {
        $data = Filemanager::initFolder(Filemanager::deleteFile($folder, $request->name));
        $data['user'] = JWTAuth::toUser($request->token);
        return response()->json(['msg' => 'success', 'data' => $data], 202);
    }

    public function changelocationFile($folder, Request $request)
    {
        Filemanager::my_copy_all(Filemanager::pathFilemanager().$request->from, Filemanager::pathFilemanager().$request->to);
        $data = Filemanager::initFolder($folder);
        $data['user'] = JWTAuth::toUser($request->token);
        return response()->json(['msg' => 'success', 'data' => $data], 202);
    }

}