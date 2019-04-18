<?php

namespace SecretBox\Http\Controllers;

use Illuminate\Http\Request;
use SecretBox\User;
use SecretBox\Group;

class GroupController extends APIController
{
    public function index()
    {
        $user = User::find(\Auth::user()->sub);
        $groups = $user->groups->makeHidden('pivot')->sortByDesc('updated_at')->values()->all();
        return $this->sendResponse($groups, 'Groups retrieved successfully.');
    }

    public function store(Request $request)
    {
        $input = [
            'name' => $request->name
        ];

        $group = new Group($input);
        $group->initial_user = \Auth::user()->sub;
        $group->save();

        $group->users()->attach(\Auth::user()->sub);
        if ($request->users) {
            $group->users()->attach($request->users, ['enc_key' => 0]);
        }

        return $this->sendResponse($group, 'Group created successfully.');
    }
}
