<?php

namespace Clearlyip\LaravelFlagsmith\Http\Controllers;

use Flagsmith\Flagsmith;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use stdClass;

class Webhooks
{
    /**
     * Handles the feature logic based on the given request.
     *
     * @param Request $request The request object
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse
     */
    public function feature(
        Request $request,
    ): \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse {
        $event_type = $request->input('event_type');

        if ($event_type !== 'FLAG_UPDATED') {
            return response()->json(['status' => 'unknown event type'], 422);
        }

        /** @var Flagsmith $flagsmith */
        $flagsmith = App::make(Flagsmith::class);

        //No cache so nothing to update
        if (!$flagsmith->hasCache()) {
            return response('');
        }

        $state = $request->input('data.new_state');
        $identityId = $request->input('data.new_state.identity_identifier');
        $cache = $flagsmith->getCache();
        if ($cache === null) {
            return response('');
        }

        //This is specifically an identity flag change
        if (!empty($identityId)) {
            $identity = $cache->get("Identity.{$identityId}");
            if ($identity === null) {
                //No cache point exists so update all
                $flagsmith->withSkipCache(true)->getIdentityFlags($identityId);
                return response('');
            }
            $existingKey = null;
            foreach ($identity->flags as $key => $flag) {
                if ($flag->feature->id === $state['feature']['id']) {
                    $existingKey = $key;
                    break;
                }
            }

            if ($existingKey !== null) {
                $identity->flags[$existingKey] = json_decode(
                    json_encode($state),
                );
            } else {
                $identity->flags[] = json_decode(json_encode($state));
            }

            $cache->set("Identity.{$identityId}", $identity);

            return response('');
        }

        //Global cache needs to be updated
        $global = $cache->get('Global');

        if ($global === null) {
            $flagsmith->getEnvironmentFlags();
            return response('');
        }

        $existingKey = null;
        foreach ($global as $key => $flag) {
            if ($flag->feature->id === $state['feature']['id']) {
                $existingKey = $key;
                break;
            }
        }

        if ($existingKey !== null) {
            $global[$existingKey]->feature->default_enabled =
                $state['feature']['default_enabled'];
            $global[$existingKey]->feature->description =
                $state['feature']['description'];
            $global[$existingKey]->feature->initial_value =
                $state['feature']['initial_value'];
            $global[$existingKey]->feature->type = $state['feature']['type'];
            $global[$existingKey]->feature_state_value =
                $state['feature_state_value'];
            $global[$existingKey]->environment = $state['environment']['id'];
            $global[$existingKey]->identity = $state['identity'];
            $global[$existingKey]->feature_segment = $state['feature_segment'];
            $global[$existingKey]->enabled = $state['enabled'];
        } else {
            $feature = new stdClass();
            $feature->id = $state['feature']['id'];
            $feature->name = $state['feature']['name'];
            $feature->created_date = $state['feature']['created_date'];
            $feature->description = $state['feature']['description'];
            $feature->initial_value = $state['feature']['initial_value'];
            $feature->default_enabled = $state['feature']['default_enabled'];
            $feature->type = $state['feature']['type'];

            $flag = new stdClass();
            $flag->id = null; //unsure what this represents
            $flag->feature = $feature;
            $flag->feature_state_value = $state['feature_state_value'];
            $flag->environment = $state['environment']['id'];
            $flag->identity = $state['identity'];
            $flag->feature_segment = $state['feature_segment'];
            $flag->enabled = $state['enabled'];
            $global[] = $flag;
        }

        $cache->set('Global', $global);
        return response('');
    }
}
