@props(['smallTable' => false, 'class' => ''])
<div class="flex flex-col {{$smallTable ? "md:max-w-[50%] md:min-w-[50%] md:mx-auto" : "w-full"}} {{$class}}">
    <div class="-m-1.5">
        <div class="p-1.5 min-w-full inline-block align-middle">
            <div class="overflow-visible">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700 border-1 border-neutral-700 overflow-x-scroll overflow-y-visible">
                    {{$slot}}
                </table>
            </div>
        </div>
    </div>
</div>
