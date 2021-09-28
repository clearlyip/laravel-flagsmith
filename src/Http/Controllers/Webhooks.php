<?php
namespace Clearlyip\LaravelFlagsmith\Http\Controllers;

use Flagsmith\Flagsmith;
use Flagsmith\Models\Identity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class Webhooks
{
    public function feature(Request $request)
    {
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

        //This is specifically an identity flag change
        if (!empty($identityId)) {
            $identity = $cache->get("identity.{$identityId}");
            if (!is_null($identity)) {
                //TODO: This does not seem the best way to do this
                $new = true;
                foreach ($identity['flags'] as &$flag) {
                    if ($flag['feature']['id'] === $state['feature']['id']) {
                        $flag['feature_state_value'] =
                            $state['feature_state_value'];
                        $flag['enabled'] = $state['enabled'];
                        $new = false;
                        break;
                    }
                }
                if ($new) {
                    $identity['flags'][] = [
                        'id' => null,
                        'feature_state_value' => $state['feature_state_value'],
                        'enabled' => $state['enabled'],
                        'environment' => $state['environment']['id'],
                        'feature_segment' => $state['feature_segment'],
                        'feature' => [
                            'id' => $state['feature']['id'],
                            'name' => $state['feature']['name'],
                            'created_date' => $state['feature']['created_date'],
                            'description' => $state['feature']['description'],
                            'inital_value' => $state['feature']['inital_value'],
                            'default_enabled' =>
                                $state['feature']['default_enabled'],
                            'type' => $state['feature']['type'],
                        ],
                    ];
                }

                $cache->set("identity.{$identityId}", $identity);

                return response('');
            }

            //No cache point exists so update all
            $flagsmith->getIdentityByIndentity(new Identity($identityId));
            return response('');
        }

        //Global cache needs to be updated
        $global = $cache->get('global');

        //A Previous cache point exists
        if (!is_null($global)) {
            //TODO: This does not seem the best way to do this
            $new = true;
            foreach ($global as &$flag) {
                if ($flag['feature']['id'] === $state['feature']['id']) {
                    $flag['feature_state_value'] =
                        $state['feature_state_value'];
                    $flag['enabled'] = $state['enabled'];
                    $new = false;
                    break;
                }
            }
            if ($new) {
                $global[] = [
                    'id' => null,
                    'feature_state_value' => $state['feature_state_value'],
                    'enabled' => $state['enabled'],
                    'environment' => $state['environment']['id'],
                    'feature_segment' => $state['feature_segment'],
                    'feature' => [
                        'id' => $state['feature']['id'],
                        'name' => $state['feature']['name'],
                        'created_date' => $state['feature']['created_date'],
                        'description' => $state['feature']['description'],
                        'inital_value' => $state['feature']['inital_value'],
                        'default_enabled' =>
                            $state['feature']['default_enabled'],
                        'type' => $state['feature']['type'],
                    ],
                ];
            }

            $cache->set('global', $global);
            return response('');
        }

        //No cache point exists so update all
        $flagsmith->getFlags();
        return response('');
    }
}
