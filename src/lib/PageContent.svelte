<script lang="ts">
    import {onMount} from 'svelte';
    import ProgressCyclistSvelte from "./ProgressCyclist.svelte";
    import {marchActivites} from "./activitiesMarch";

    type Activity = {
        name: string;
        distance: number;
        moving_time: number;
        type: string;
        start_date: string;
        average_speed: number;
        max_speed: number;
    };

    let isLoading = $state(false);
    let error: string | null = $state(null);
    let totalDistance = $state(0);
    let totalTime = $state(0);
    let completedPercentage = $state(0);
    let animateCompletedPercentage = $state(0);

    const activities: Activity[] = marchActivites;

    const fetchActivities = async () => {
        try {
            // const marchStart = new Date('2025-03-01').getTime() / 1000;
            // const marchEnd = new Date('2025-04-01').getTime() / 1000;
            //
            // const response = await fetch(`https://track-joe.josephcorcoran.co.uk/api/strava.php?route=activities&after=${marchStart}&before=${marchEnd}`, {
            //     method: 'GET',
            //     credentials: 'include'
            // });
            //
            // if (!response.ok) {
            //     throw new Error('Failed to fetch activities');
            // }
            //
            // const data: Activity[] = await response.json();
            // activities = data.filter(activity => activity.type === 'Ride');

            totalDistance = activities.reduce((sum, activity) => sum + activity.distance, 0);
            totalTime = activities.reduce((sum, activity) => sum + activity.moving_time, 0);

            const distanceInMiles = totalDistance / 1609.34;

            completedPercentage = distanceInMiles / 500 * 100;

        } catch (err) {
            error = err instanceof Error ? err.message : 'An error occurred';
        } finally {
            isLoading = false;
        }
    }

    onMount(() => {
        fetchActivities();
    });

    const distanceInKm = (meters: number) => `${(meters / 1000).toFixed(1)}km`;
    const distanceInMiles = (meters: number) => `${(meters / 1609.34).toFixed(1)}mi`;
    const speedInKmh = (metersPerSecond: number) => `${(metersPerSecond * 3.6).toFixed(2)} km/h`;
    const speedInMph = (metersPerSecond: number) => `${(metersPerSecond * 2.237).toFixed(2)} mph`;

    const formatTime = (seconds: number) => {
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        return `${hours}h ${minutes}m`;
    }

    const formatDate = (dateString: string) => {
        return new Date(dateString).toLocaleDateString();
    }

    const slowlyIncrement = (startValue: number,
                             endValue: number,
                             duration: number,
                             callback: (num: number) => void) => {
        const stepTime = 16;
        const steps = duration / stepTime;
        const increment = (endValue - startValue) / steps;

        let currentValue = startValue;
        let stepCount = 0;

        const intervalId = setInterval(() => {
            currentValue += increment;
            stepCount++;

            if (stepCount >= steps) {
                currentValue = endValue;
                clearInterval(intervalId);
            }

            callback(currentValue);
        }, stepTime);
    }

    $effect(() => {
        slowlyIncrement(0, completedPercentage, 2000, (num) => {
            animateCompletedPercentage = Math.min(num, 100);
        })
    })

</script>

<div class="max-w-2xl mx-auto p-4">
    <p class="mb-2">I am raising money for Cancer Research UK, by challenging myself to cycle over 500 miles in March
        2025 😅.</p>
    <a class="mb-2" href="https://fundraise.cancerresearchuk.org/donate/e634691b-5eae-4bef-ae0c-b20fabb09ec3/details"
       target="_blank">
        <div class="text-center text-xl bg-[#F033A3] p-2 rounded-3xl hover:bg-[#F053A3] click:bg-green-300">
            Please donate here (Cancer Research UK page)
        </div>
    </a>
    <a class="text-blue-500 text-sm mt-1 text-center w-full block hover:text-blue-700"
       href="https://fundraise.cancerresearchuk.org/page/josephs-giving-page-612" target="_blank">
        Full fundraiser page here
    </a>

    <h3 class="text-center font-semibold mt-4">
        Progress: {#if animateCompletedPercentage >= 100}🎉{/if}
    </h3>
    <ProgressCyclistSvelte completedPercentage={animateCompletedPercentage}/>

    {#if isLoading}
        <p class="text-gray-600 pt-4 text-center">Loading activities...</p>
    {:else if error}
        <p class="text-red-500">{error}</p>
    {:else}
        <div class="bg-white rounded-lg shadow p-6 my-6">
            <div class="grid grid-cols-6 md:grid-cols-4 gap-2">
                <div class="text-center col-span-4 md:col-span-2">
                    <h3 class="text-lg font-semibold">Total Distance</h3>
                    <p class="text-xl md:text-2xl text-blue-600">{distanceInMiles(totalDistance)}/500mi target</p>
                    <p class="text-blue-600">{distanceInKm(totalDistance)}/805km target</p>
                </div>
                <div class="text-center col-span-2">
                    <h3 class="text-lg font-semibold">Total Time</h3>
                    <p class="text-xl md:text-2xl text-blue-600">{formatTime(totalTime)}</p>
                </div>
            </div>
        </div>
        <div class="space-y-4">
            {#each activities as activity}
                <div class="bg-white rounded-lg shadow p-4">
                    <h3 class="font-semibold">{activity.name}</h3>
                    <div class="grid grid-cols-3 md:grid-cols-7 gap-2 mt-2 text-sm text-gray-600">
                        <div>{formatDate(activity.start_date)}</div>
                        <div class="col-span-2">🚴‍♂️ {distanceInMiles(activity.distance)}
                            ({distanceInKm(activity.distance)})
                        </div>
                        <div class="col-span-2">
                            🔥Av. speed {speedInMph(activity.average_speed)} ({speedInKmh(activity.average_speed)})
                        </div>
                        <div class="col-span-2">
                            🔥Max speed {speedInMph(activity.max_speed)} ({speedInKmh(activity.max_speed)})
                        </div>
                        <div>⏱️ {formatTime(activity.moving_time)}</div>
                    </div>
                </div>
            {/each}
        </div>
    {/if}
</div>

