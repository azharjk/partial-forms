<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\PartialForm;
use App\Models\User;

class PartialFormController extends Controller
{
    public function __invoke(Request $request)
    {
        $partValue = json_encode($request->json()->all());
        $formType = $request->query('form-type');

        $pfid = $request->query('pfid');
        if (! $pfid) {
            $partialForm = PartialForm::create([
                'part_value' => $partValue,
                'form_type' => $formType
            ]);

            return [
                'pf_id' => $partialForm->id,
                'pf_part_value' => json_decode($partialForm->part_value, true)
            ];
        }

        $partialForm = $this->updatePartialForm($request, $pfid);

        if ($this->isPartialFormLogin($partialForm)) {
            // DO LOGIN
        } elseif ($this->isPartialFormRegister($partialForm)) {
            $cred = json_decode($partialForm->part_value, true);
            $token = $this->authRegister($cred);

            return ['token' => $token];
        }

        return [
            'pf_id' => $partialForm->id,
            'pf_part_value' => json_decode($partialForm->part_value, true)
        ];
    }

    public function authRegister($cred)
    {
        // DO A VALIDATION
        $user = User::create($cred);
        $token = $user->createToken('auth-token');

        return $token->plainTextToken;
    }

    public function isPartialFormLogin(PartialForm $partialForm) {
        return $partialForm->form_type === 'login' && $this->validatePartialFormLogin($partialForm);
    }

    public function isPartialFormRegister(PartialForm $partialForm) {
        return $partialForm->form_type === 'register' && $this->validatePartialFormRegister($partialForm);
    }

    public function updatePartialForm(Request $request, $pfid)
    {
        $partialForm = PartialForm::find($pfid);

        $data = json_decode($partialForm->part_value, true);
        $merged = array_merge($data, $request->json()->all());

        $partialForm->part_value = json_encode($merged);

        $partialForm->save();

        return $partialForm;
    }

    public function validatePartialFormLogin(PartialForm $partialForm) {
        $loginKeys = ['username' => 1, 'password' => 1];
        $partValue = json_decode($partialForm->part_value, true);

        $diff = array_diff_key($loginKeys, $partValue);

        if (count($diff) > 0) {
            return;
        }

        // DO LOGIN
        return;
    }

    public function validatePartialFormRegister(PartialForm $partialForm) {
        $registerKeys = ['name' => 1, 'email' => 1, 'password' => 1];
        $partValue = json_decode($partialForm->part_value, true);

        $diff = array_diff_key($registerKeys, $partValue);

        if (count($diff) > 0) {
            return false;
        }

        return true;
    }
}
