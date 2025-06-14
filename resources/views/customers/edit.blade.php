<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Edit Customer '{{$customer->customer_name}}'"/>
        <x-error />
        <x-success />
        <form action="{{route('customers.update', compact('customer'))}}" method="POST" class="flex flex-col gap-4">
            @csrf
            @method('PUT')
            {{--Customer name--}}
            <div class="max-w-sm">
                <label for="customer_name" class="block text-sm font-medium mb-2 dark:text-white">Customer Name</label>
                <input type="text" id="customer_name" name="customer_name" value="{{$customer->customer_name}}" class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
            </div>
            {{--Customer address--}}
            <div class="max-w-sm">
                <label for="customer_address_1" class="block text-sm font-medium mb-2 dark:text-white">Customer Address Line 1</label>
                <input type="text" id="customer_address_1" name="customer_address_1" value="{{$customer->customer_address_1}}" class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
            </div>
            <div class="max-w-sm">
                <label for="customer_address_2" class="block text-sm font-medium mb-2 dark:text-white">Customer Address Line 2 (optional)</label>
                <input type="text" id="customer_address_2" name="customer_address_2" value="{{$customer->customer_address_2}}" class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
            </div>
            <div class="max-w-sm">
                <label for="customer_postcode" class="block text-sm font-medium mb-2 dark:text-white">Customer Postcode</label>
                <input type="text" id="customer_postcode" name="customer_postcode" value="{{$customer->customer_postcode}}" class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
            </div>
            <div class="max-w-sm">
                <label for="customer_city" class="block text-sm font-medium mb-2 dark:text-white">Customer City</label>
                <input type="text" id="customer_city" name="customer_city" value="{{$customer->customer_city}}" class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
            </div>
            <div class="max-w-sm">
                <label for="customer_country" class="block text-sm font-medium mb-2 dark:text-white">Customer Country</label>
                <input type="text" id="customer_country" name="customer_country" value="{{$customer->customer_country}}" class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
            </div>
            {{--Customer email--}}
            <div class="max-w-sm">
                <label for="customer_email" class="block text-sm font-medium mb-2 dark:text-white">Customer Email (optional)</label>
                <input type="email" id="customer_email" name="customer_email" value="{{$customer->customer_email}}" class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
            </div>
            {{--Customer phone--}}
            <div class="max-w-sm">
                <label for="customer_phone" class="block text-sm font-medium mb-2 dark:text-white">Customer Phone (optional)</label>
                <input type="text" id="customer_phone" name="customer_phone" value="{{$customer->customer_phone}}" class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
            </div>
            <flux:button variant="primary" type="submit">Update</flux:button>
        </form>
    </x-page-section>
</x-flux-layout>
