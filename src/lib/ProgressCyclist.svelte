<script lang="ts">
    let trackPathElement: SVGPathElement | undefined = $state();
    let heightAndDist = $state({height: 0, dist: 0});

    const { completedPercentage = 0 } = $props<{completedPercentage: number}>()

    const getHeightAtPercentage = (percentage: number)  => {
        if (!trackPathElement) return 0;
        const length = trackPathElement.getTotalLength();
        const point = trackPathElement.getPointAtLength((percentage / 100) * length);
        return point.y;
    }

    $effect(() => {
        const heightOnLine = getHeightAtPercentage(completedPercentage);
        const distAlongLine = (completedPercentage / 100) * 200;
        heightAndDist = {height: heightOnLine, dist: distAlongLine};
    })

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
        <circle r="5" fill="#3b82f6" cx={heightAndDist.dist} cy={heightAndDist.height} />
        <text font-style="italic" fill="#000" font-size="5" x="0" y="39">Start</text>
        <text font-style="italic" fill="#000" font-size="5" x="175" y="5">Finish 🏁</text>
    </svg>
</div>
