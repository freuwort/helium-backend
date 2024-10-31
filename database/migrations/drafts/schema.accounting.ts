enum UnitType {
    LENGTH = 'length',
    AREA = 'area',
    VOLUME = 'volume',
    MASS = 'mass',
    TIME = 'time',
    TEMPERATURE = 'temperature',
    ELECTRIC_CURRENT = 'electric_current',
    LUMINOUS_INTENSITY = 'luminous_intensity',
    AMOUNT_OF_SUBSTANCE = 'amount_of_substance',
    ANGLE = 'angle',
    DIGITAL = 'digital',
    CURRENCY = 'currency',
    OTHER = 'other',
}

enum AccountingDocumentStatus {
    DRAFT = 'draft',
    PENDING = 'pending',
    COMPLETED = 'completed',
    CANCELLED = 'cancelled',
}

enum AccountingDocumentItemType {
    PRODUCT = 'product',
    SERVICE = 'service',
    SHIPPING = 'shipping',
    DISCOUNT = 'discount',
}

enum AccountingDocumentType {
    QUOTE = 'quote',
    ORDER = 'order',
    INVOICE = 'invoice',
    REFUND = 'refund',
}


type Address = {
    id: Number,
    address_line_1: String,
    address_line_2: String,
    postal_code: String,
    city: String,
    state: String,
    country_code: String,
    notes: String | null,
    longitude: Number | null,
    latitude: Number | null,
}

type Unit = {
    code: String,
    name: String,
    symbol: String,
    type: UnitType,
}

type Currency = {
    code: String,
    name: String,
    symbol: String,
    decimal_places: Number,
}


type AccountingDocumentParty = {
    id: Number,
    version: Number,
    name: String,
    partyable_id: Number | null
    partyable_type: String | null,
    main_address: Address['id'] | null,
    billing_address: Address['id'] | null,
    shipping_address: Address['id'] | null,
    vat_id: String | null,
    tax_id: String | null,
    customer_id: String | null,
    supplier_id: String | null,
    employee_id: String | null,
    contact_person: String | null,
}

type AccountingDocumentItem = {
    id: Number,
    document_id: Number,
    itemable_id: Number | null,
    itemable_type: String | null,
    type: AccountingDocumentItemType,
    description: String,
    quantity: Number,
    unit: Unit['code'],
    tax_rate: Number,
    price_net: Number,
    price_gross: Number,
    price_tax: Number,
}

type AccountingDocument = {
    id: Number,
    type: AccountingDocumentType,
    status: AccountingDocumentStatus,

    sender: AccountingDocumentParty['id'],
    recipient: AccountingDocumentParty['id'],

    quote_id: String | null,
    order_id: String | null,
    invoice_id: String | null,
    refund_id: String | null,
    reference_id: String | null,

    subject: String,
    description: String,
    footer: String,

    items: AccountingDocumentItem[],
    
    currency: Currency['code'],
    total_net: Number,
    total_gross: Number,
    total_tax: Number,

    issue_date: Date | null,
    valid_date: Date | null,
    due_date: Date | null,
    paid_date: Date | null,
    delivery_date: Date | null,
    shipment_date: Date | null,

    created_at: Date,
    updated_at: Date,
}


type Quote = AccountingDocument & {
    type: AccountingDocumentType.QUOTE,
    quote_id: String,
    issue_date: Date,
}

type Order = AccountingDocument & {
    type: AccountingDocumentType.ORDER,
    order_id: String,
    issue_date: Date,
    delivery_date: Date,
}

type Invoice = AccountingDocument & {
    type: AccountingDocumentType.INVOICE,
    invoice_id: String,
    issue_date: Date,
    due_date: Date,
    delivery_date: Date,
}

type Refund = AccountingDocument & {
    type: AccountingDocumentType.REFUND,
    refund_id: String,
    issue_date: Date,
}



const invoice: Invoice = {
    id: 1,
    type: AccountingDocumentType.INVOICE,
    status: AccountingDocumentStatus.DRAFT,
    
    sender: 1,
    recipient: 2,
    
    quote_id: null,
    order_id: null,
    invoice_id: 'INV-2021-001',
    refund_id: null,
    reference_id: 'REF-2021-001',
    
    subject: 'Invoice #1',
    description: 'This is the first invoice',
    footer: 'Thank you for your business',
    
    items: [
        {
            id: 1,
            document_id: 1,
            itemable_id: null,
            itemable_type: null,
            type: AccountingDocumentItemType.PRODUCT,
            description: 'Product 1',
            quantity: 1,
            unit: 'pcs',
            tax_rate: 0.19,
            price_net: 100,
            price_gross: 119,
            price_tax: 19,
        },
    ],
    
    currency: 'EUR',
    total_net: 100,
    total_gross:  119,
    total_tax: 19,
    
    issue_date: new Date('2021-01-01'),
    valid_date: null,
    due_date: new Date('2021-01-31'),
    paid_date: null,
    delivery_date: new Date('2021-01-01'),
    shipment_date: null,

    created_at: new Date('2021-01-01T00:00:00Z'),
    updated_at: new Date('2021-01-01T00:00:00Z'),
}