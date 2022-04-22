<?php

namespace App\Http\Controllers;

use App\Models\PartialForm;
use Illuminate\Http\Request;

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

            return ['pfid' => $partialForm->id];
        }

        $partialForm = $this->updatePartialForm($request, $pfid);

        if ($partialForm->form_type === 'login') {
            $this->partialFormLogin($partialForm);
        } elseif ($partialForm->form_type === 'register') {
            $this->partialFormRegister($partialForm);
        }
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

    public function partialFormLogin(PartialForm $partialForm) {
        $loginKeys = ['username' => 1, 'password' => 1];
        $partValue = json_decode($partialForm->part_value, true);

        $diff = array_diff_key($loginKeys, $partValue);

        if (count($diff) > 0) {
            return;
        }

        // DO LOGIN
        return;
    }

    public function partialFormRegister(PartialForm $partialForm) {
        $registerKeys = ['first_name' => 1, 'last_name' => 1, 'username' => 1, 'password' => 1];
        $partValue = json_decode($partialForm->part_value, true);

        $diff = array_diff_key($registerKeys, $partValue);

        if (count($diff) > 0) {
            dd($partValue);
            return;
        }

        // DO REGISTER
        return;
    }
}
