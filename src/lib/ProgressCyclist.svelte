<script lang="ts">
    import {Tween} from 'svelte/motion';
    import {linear} from 'svelte/easing';

    let trackPathElement: SVGPathElement | undefined = $state();
    let position = $state({x: 0, y: 40});

    const percentageTween = new Tween(0, {
        duration: 400,
        easing: linear
    });

    const {completedPercentage = 0} = $props<{ completedPercentage: number }>()

    const getPositionAtPercentage = (percentage: number) => {
        if (!trackPathElement) return {x: 0, y: 40};
        const length = trackPathElement.getTotalLength();
        const point = trackPathElement.getPointAtLength((percentage / 100) * length);
        return {x: point.x, y: point.y};
    }

    $effect(() => {
        percentageTween.set(completedPercentage);
    });

    // This effect subscribes to changes in the tweened value
    $effect(() => {
        // Get the current value of the tween
        const currentValue = percentageTween.current;
        position = getPositionAtPercentage(currentValue);
    });
</script>

<div class="w-full">
    <svg viewBox="0 0 200 40">
        <path
                id="trackPath"
                bind:this={trackPathElement}
                d="M 0,40 L 20,20 40,23 60,10 80,20 100,35 120,20 140,30 160,5 180,15 200,10"
                stroke="#e5e7eb"
                stroke-width="1"
                fill="none"
        />
        <circle r="5" fill="#3b82f6" cx={position.x} cy={position.y}/>
        <text font-style="italic" fill="#000" font-size="5" x="0" y="39">Start</text>
        <text font-style="italic" fill="#000" font-size="5" x="175" y="5">Finish 🏁</text>
    </svg>
</div>
