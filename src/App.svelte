<script lang="ts">
  import PageContent from './lib/PageContent.svelte'
  import stravaIcon from './assets/strava.svg';
  import svelteIcon from './assets/svelte.svg';
</script>

<main>
    <header class="fixed flex justify-center items-center top-0 p-2 bg-gray-400 w-full">
        <span class="text-center md:text-xl">Track Joe's progress - 2025 March 500 mile challenge</span>
    </header>
  <div class="px-2 md:px-10 py-14 md:py-10 flex">
      <PageContent />
  </div>
    <footer class="fixed flex justify-center items-center text-sm bottom-0 p-1 bg-gray-400 w-full">
        <span>Powered by</span>
        <a target="_blank" href="https://www.strava.com/"><img alt="strava icon" src={stravaIcon} /></a>
       <span>built by  <a class="hover:text-white" target="_blank" href="https://www.josephcorcoran.co.uk/">Joe Corcoran</a> with Svelte</span>
        <a target="_blank" href="https://svelte.dev/"><img alt="svelte icon" src={svelteIcon} /></a>
    </footer>
</main>
