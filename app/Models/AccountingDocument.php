<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'status',
        'quote_id',
        'order_id',
        'invoice_id',
        'refund_id',
        'reference_id',
        'subject',
        'description',
        'footer',
        'currency',
        'total_net',
        'total_gross',
        'total_tax',
        'issue_date',
        'valid_date',
        'due_date',
        'paid_date',
        'delivery_date',
        'shipping_date',
    ];

    protected $casts = [
        'issue_date' => 'datetime',
        'valid_date' => 'datetime',
        'due_date' => 'datetime',
        'paid_date' => 'datetime',
        'delivery_date' => 'datetime',
        'shipping_date' => 'datetime',
    ];



    public function sender()
    {
        return $this->belongsTo(AccountingContact::class, 'sender_id');
    }

    public function recipient()
    {
        return $this->belongsTo(AccountingContact::class, 'recipient_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency');
    }

    public function items()
    {
        return $this->hasMany(AccountingDocumentItem::class);
    }
}
