<!--<script lang="ts">-->

<!--</script>-->
<!--<div class="flex flex-wrap">-->
<!--  <h1 class="text-3xl w-full">Track my progress completing 500 miles of cycling in March 2025</h1>-->
<!--  <p>Hi I am raising money for Cancer Research UK by challenging myself to cycle over 500 miles in March 2025</p>-->
<!--  <p>Hi I am raising money for Cancer Research UK by challenging myself to cycle over 500 miles in March 2025</p>-->
<!--</div>-->

<script lang="ts">
  import { onMount } from 'svelte';

  type Activity = {
    name: string;
    distance: number;
    moving_time: number;
    type: string;
    start_date: string;
    average_speed: number;
  };

  let activities: Activity[] = [];
  let isLoading = true;
  let error: string | null = null;
  let totalDistance = 0;
  let totalTime = 0;

  const fetchActivities = async () => {
    try {
      const marchStart = new Date('2025-03-01').getTime() / 1000;
      const marchEnd = new Date('2025-03-31').getTime() / 1000;

      const response = await fetch(`https://track-joe.josephcorcoran.co.uk/api/strava.php?route=activities&after=${marchStart}&before=${marchEnd}`, {
        method: 'GET',
        credentials: 'include'
      });

      if (!response.ok) {
        throw new Error('Failed to fetch activities');
      }

      const data: Activity[] = await response.json();

      activities = data.filter(activity => activity.type === 'Ride');

      totalDistance = activities.reduce((sum, activity) => sum + activity.distance, 0);
      totalTime = activities.reduce((sum, activity) => sum + activity.moving_time, 0);

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

  const formatDate = (dateString: string)  => {
    return new Date(dateString).toLocaleDateString();
  }
</script>

<div class="max-w-2xl mx-auto p-4">
  <p class="mb-2">I am raising money for Cancer Research UK, by challenging myself to cycle over 500 miles in March 2025 😅.</p>
  <a class="mb-2" href="https://fundraise.cancerresearchuk.org/donate/e634691b-5eae-4bef-ae0c-b20fabb09ec3/details"  target="_blank">
    <div class="text-center text-xl bg-[#F033A3] p-2 rounded-3xl hover:bg-[#F053A3] click:bg-green-300">
      Please donate here real quick (Cancer Research UK page)
    </div>
  </a>
  <a class="text-blue-500 text-sm mt-1 text-center w-full block" href="https://fundraise.cancerresearchuk.org/page/josephs-giving-page-612" target="_blank">Full fundraiser page here</a>
    {#if isLoading}
    <p class="text-gray-600 mt-2">Loading activities...</p>
  {:else if error}
    <p class="text-red-500">{error}</p>
  {:else}
    <div class="bg-white rounded-lg shadow p-6 my-6">
      <div class="grid grid-cols-2 gap-4">
        <div class="text-center">
          <h3 class="text-lg font-semibold">Total Distance</h3>
          <p class="text-2xl text-blue-600">{distanceInMiles(totalDistance)} / 500 mile target</p>
          <p class="text-md text-blue-600">({distanceInKm(totalDistance)}) / 805 km target</p>
        </div>
        <div class="text-center">
          <h3 class="text-lg font-semibold">Total Time</h3>
          <p class="text-2xl text-blue-600">{formatTime(totalTime)}</p>
        </div>
      </div>
    </div>

    <div class="space-y-4">
      {#each activities as activity}
        <div class="bg-white rounded-lg shadow p-4">
          <h3 class="font-semibold">{activity.name}</h3>
          <div class="grid grid-cols-3 md:grid-cols-6 gap-2 mt-2 text-sm text-gray-600">
            <div>{formatDate(activity.start_date)}</div>
            <div class="col-span-2">🚴‍♂️ {distanceInMiles(activity.distance)} ({distanceInKm(totalDistance)})</div>
            <div class="col-span-2">
              🔥{speedInMph(activity.average_speed)} ({speedInKmh(activity.average_speed)})
            </div>
            <div>⏱️ {formatTime(activity.moving_time)}</div>
          </div>
        </div>
      {/each}
    </div>
  {/if}
</div>

