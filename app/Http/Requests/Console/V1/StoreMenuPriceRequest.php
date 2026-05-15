<?php

namespace App\Http\Requests\Console\V1;

use App\Models\Inventory;
use App\Models\InventoryHistory;
use Illuminate\Foundation\Http\FormRequest;

class StoreMenuPriceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'price' => ['numeric', 'required', 'min:1'],
            'recipes' => ['required', 'array'],
            'recipes.*.uuid' => ['required', 'distinct'],
            'recipes.*.is_custom' => ['nullable', 'boolean'],
            'recipes.*.custom_price' => ['nullable', 'numeric', 'min:1'],
            'recipes.*.qty' => ['required', 'numeric', 'min:1'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            foreach ($this->input('recipes', []) as $index => $recipe) {
                $isCustom = filter_var($recipe['is_custom'] ?? false, FILTER_VALIDATE_BOOLEAN);

                if ($isCustom) {
                    if (empty($recipe['custom_price'])) {
                        $validator->errors()->add("recipes.{$index}.custom_price", 'Harga manual wajib diisi.');
                    }
                    if (! Inventory::where('uuid', $recipe['uuid'])->exists()) {
                        $validator->errors()->add("recipes.{$index}.uuid", 'Bahan tidak ditemukan.');
                    }
                } else {
                    if (! InventoryHistory::where('uuid', $recipe['uuid'])->exists()) {
                        $validator->errors()->add("recipes.{$index}.uuid", 'Riwayat restock tidak ditemukan.');
                    }
                }
            }
        });
    }
}
