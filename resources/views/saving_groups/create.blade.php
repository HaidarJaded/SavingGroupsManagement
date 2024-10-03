@extends('layouts.sidebar')

@section('content')
<div class="container">
    <h1>Create New Saving Group</h1>

    <form id="saving-group-form" action="{{ route('saving_groups.store') }}" method="POST">
        @csrf

        <!-- Saving Group Fields -->
        <div class="form-group">
            <label for="name">اسم الجمعية:</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            @error('name')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label for="start_date">تاريخ بدء الجمعية:</label>
            <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}" required>
            @error('start_date')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label for="amount_per_day">المبلغ باليوم:</label>
            <input type="number" name="amount_per_day" min="1" class="form-control" value="{{ old('amount_per_day') }}"
                required>
            @error('amount_per_day')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="days_per_cycle">طول الدور:</label>
            <input type="number" name="days_per_cycle" min="1" class="form-control" value="{{ old('days_per_cycle') }}"
                required>
            @error('days_per_cycle')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <!-- Subscriber Input Fields -->
        <h3>المشتركون:</h3>
        <h5 style="color:red">الرجاء الادخال بالترتيب</h5>
        <div class="form-group">
            <label for="subscriber_name">الاسم:</label>
            <input type="text" id="subscriber_name" class="form-control" placeholder="أدخل اسم المشترك">

            <label for="subscriber_last_name">الكنية:</label>
            <input type="text" id="subscriber_last_name" class="form-control" placeholder="أدخل كنية المشترك">

            <label for="subscriber_phone">رقم الهاتف (اختياري):</label>
            <input type="text" id="subscriber_phone" class="form-control" placeholder="أدخل رقم هاتف المشترك">

            <button type="button" id="add-subscriber" class="btn btn-secondary mt-3">أضافة المشترك إلى اللائحة</button>
        </div>

        <!-- List of Added Subscribers -->
        <div class="form-group">
            <h4>لائحة المشتركين:</h4>
            @error('subscribers')
                <span class="text-danger">{{ $message }}</span>
            @enderror
            <ul id="subscribers-list" class="list-group">
                <!-- List of subscribers will appear here -->
            </ul>
        </div>

        <!-- Hidden Fields to Store Subscribers Data for Submission -->
        <div id="subscribers-wrapper">
            <!-- Hidden inputs for subscribers will be added here dynamically -->
        </div>

        <!-- Hidden Field for Subscribers Count -->
        <input type="hidden" id="subscribers_count" name="subscribers_count" value="0">

        <button type="submit" class="btn btn-primary mt-3">إنشاء الجمعية</button>
    </form>

    <script>
        let subscriberCount = 0; // Counter to track number of subscribers
        const addSubscriberButton = document.getElementById('add-subscriber');
        const subscribersList = document.getElementById('subscribers-list');
        const subscribersWrapper = document.getElementById('subscribers-wrapper');
        const subscribersCountInput = document.getElementById('subscribers_count'); // Hidden input for subscribers count

        addSubscriberButton.addEventListener('click', function () {
            const firstName = document.getElementById('subscriber_name').value;
            const lastName = document.getElementById('subscriber_last_name').value;
            const phone = document.getElementById('subscriber_phone').value;

            if (!(firstName && lastName  )) {
                alert('الرجاء إدخال اسم وكنية المشترك.');
                return;
            }
            // Add to the visible list
            const listItem = document.createElement('li');
            listItem.classList.add('list-group-item');
            listItem.innerHTML = `${firstName} ${lastName} (${phone}) <button type="button" class="btn btn-danger btn-sm float-right remove-subscriber">Remove</button>`;
            subscribersList.appendChild(listItem);

            // Add hidden input fields for submission
            const hiddenFirstNameInput = document.createElement('input');
            hiddenFirstNameInput.type = 'hidden';
            hiddenFirstNameInput.name = `subscribers[${subscriberCount}][name]`;
            hiddenFirstNameInput.value = firstName;
            hiddenFirstNameInput.classList.add(`subscriber-${subscriberCount}`);
            subscribersWrapper.appendChild(hiddenFirstNameInput);

            const hiddenLastNameInput = document.createElement('input');
            hiddenLastNameInput.type = 'hidden';
            hiddenLastNameInput.name = `subscribers[${subscriberCount}][last_name]`;
            hiddenLastNameInput.value = lastName;
            hiddenLastNameInput.classList.add(`subscriber-${subscriberCount}`);
            subscribersWrapper.appendChild(hiddenLastNameInput);

            const hiddenPhoneInput = document.createElement('input');
            hiddenPhoneInput.type = 'hidden';
            hiddenPhoneInput.name = `subscribers[${subscriberCount}][phone]`;
            hiddenPhoneInput.value = phone;
            hiddenPhoneInput.classList.add(`subscriber-${subscriberCount}`);
            subscribersWrapper.appendChild(hiddenPhoneInput);

            const hiddenRankInput = document.createElement('input');
            hiddenPhoneInput.type = 'hidden';
            hiddenPhoneInput.name = `subscribers[${subscriberCount}][rank]`;
            hiddenPhoneInput.value = subscriberCount+1;
            hiddenPhoneInput.classList.add(`subscriber-${subscriberCount}`);
            subscribersWrapper.appendChild(hiddenPhoneInput);

            subscriberCount++;

            // Update the subscribers count field
            subscribersCountInput.value = subscriberCount;

            // Clear input fields
            document.getElementById('subscriber_name').focus();
            document.getElementById('subscriber_name').value = '';
            document.getElementById('subscriber_last_name').value = '';
            document.getElementById('subscriber_phone').value = '';
            document.getElementById('subscriber_rank').value = '';

        });

        // Event delegation for dynamically created "Remove" buttons
        subscribersList.addEventListener('click', function (event) {
            if (event.target.classList.contains('remove-subscriber')) {
                const listItem = event.target.closest('li'); // Find the <li> that contains the clicked button
                const index = Array.from(subscribersList.children).indexOf(listItem); // Get the index of the <li> in the list

                // Remove corresponding hidden inputs from the form
                document.querySelectorAll(`.subscriber-${index}`).forEach(input => input.remove());

                // Remove the <li> from the visible list
                listItem.remove();

                // Decrement the subscribers count
                subscriberCount--;
                subscribersCountInput.value = subscriberCount; // Update the hidden count input

                // Adjust class names of remaining inputs to maintain proper indexing
                document.querySelectorAll('#subscribers-wrapper input').forEach((input, i) => {
                    input.className = `subscriber-${i}`;
                });
            }
        });
    </script>
</div>
@endsection