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
                    $group->users()->attach($userId, ['enc_key' => $request->encKeys[$id]]);
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
}
