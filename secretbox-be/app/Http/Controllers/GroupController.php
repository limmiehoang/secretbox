<?php

namespace SecretBox\Http\Controllers;

use Illuminate\Http\Request;
use SecretBox\EncFile;
use SecretBox\User;
use SecretBox\Group;

class GroupController extends APIController
{
    public function index()
    {
        $user = User::find(\Auth::user()->sub);
        $newGroups = $user->newGroups->sortByDesc('updated_at')->values()->all();
        $joinedGroups = $user->joinedGroups->sortByDesc('updated_at')->values()->all();
        $groups = [
          'newGroups' => $newGroups,
          'joinedGroups' => $joinedGroups
        ];
        return $this->sendResponse($groups, 'Groups retrieved successfully.');
    }

    public function store(Request $request)
    {
        if (Group::where('name', $request->name)->exists()) {
            return $this->sendError('Group name exists.', [], 303);
        }

        $groupInput = [
            'name' => $request->name
        ];

        $group = new Group($groupInput);
        $group->initial_user = \Auth::user()->sub;
        $group->identity_key = $request->identityKey; //@TODO: get Auth0 User Management API in server-side
        $group->save();

        $group->users()->attach(\Auth::user()->sub);
        if ($request->users) {
            if ($request->encKeys) {
                foreach($request->users as $id=>$userId) {
                    try {
                        $group->users()->attach($userId, ['enc_key' => $request->encKeys[$id]]);
                    } catch (\Exception $e) {

                    }
                }
            }
        }

        $fileInput = [
            'group_id' => $group->id,
            'enc_metadata' => $request->encMetadata
        ];

        $initialFile = new EncFile($fileInput);
        $initialFile->save();

        $group->initial_data = $initialFile->id;
        $group->save();

        $group->initialData;

        return $this->sendResponse($group, 'Group created successfully.');
    }

    public function show($id)
    {
        $group = Group::find($id);


        if (is_null($group)) {
            return $this->sendError('Group not found.');
        }

        if(is_null($group->users->where('sub', \Auth::user()->sub))) {
            return $this->sendError('You cannot access this resource.', [], 401);
        }

        $group->initialData;
        $group->encFiles;
        $group->key_info = $group->users->where('sub', \Auth::user()->sub)->first()->pivot;
        $group->users->makeHidden('pivot');

        return $this->sendResponse($group, 'Group retrieved successfully.');
    }
}
