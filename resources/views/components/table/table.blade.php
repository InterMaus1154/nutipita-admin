@props(['smallTable' => false])
<div class="flex flex-col {{$smallTable ? "md:max-w-[50%] md:min-w-[50%] md:mx-auto" : "w-full"}} ">
    <div class="-m-1.5 overflow-x-auto">
        <div class="p-1.5 min-w-full inline-block align-middle">
            <div class="overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700 border-1 border-neutral-700 ">
                    {{$slot}}
                </table>
            </div>
        </div>
    </div>
</div>
