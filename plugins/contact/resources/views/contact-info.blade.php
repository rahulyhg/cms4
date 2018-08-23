@if ($contact)
    <p>{{ trans('plugins.contact::contact.tables.fullname') }}: {{ $contact->name }}</p>
    <p>{{ trans('plugins.contact::contact.tables.email') }}: {{ $contact->email }}</p>
    <p>{{ trans('plugins.contact::contact.tables.phone') }}: {{ $contact->phone }}</p>
    <p>{{ trans('plugins.contact::contact.tables.address') }}: {{ $contact->address }}</p>
    <p>{{ trans('plugins.contact::contact.tables.subject') }}: {{ $contact->subject }}</p>
    <p>{{ trans('plugins.contact::contact.tables.content') }}: {{ $contact->content }}</p>
@endif
