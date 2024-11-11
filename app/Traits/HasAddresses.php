<?php

namespace App\Traits;

use App\Models\Address;

trait HasAddresses
{
    public function updateAddress(string $type, array|null $address): bool
    {
        if (!in_array($type, $this->address_types)) {
            return false;
        }

        $selector = $type.'_address_id';
        $id = $this->{$selector};

        // Delete address if null
        if ($address === null) {
            Address::destroy($id);
            return true;
        }

        // Update or create address...
        $address = Address::updateOrCreate(['id' => $id], $address);

        // ...and assign it to model
        $this->{$selector} = $address->id;
        $this->save();

        return true;
    }
}