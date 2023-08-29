<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 ">
                    <div class="pb-6">
                    <strong>Your balance is: {{$user->balance}}</strong> <br> <br>
                    <form action="/increment" method="post">
                        @csrf
                        <input type="text" name="price" >
                        <select name="other_user_id" id="">
                            @foreach($other_users as $item)
                                <option value="{{$item->id}}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                        <input type="submit" class="border border-primary-500 p-2 bg-black/50 " value="Increment">
                    </form>
                </div>
                <div>
                    <form action="/decrement" method="post">
                        @csrf
                        <input type="text" name="price" >
                        <select name="other_user_id" id="">
                            @foreach($other_users as $item)
                                <option value="{{$item->id}}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                        <input type="submit" class="border border-primary-500 p-2 bg-black/50 " value="Decrement">
                    </form>
                </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
