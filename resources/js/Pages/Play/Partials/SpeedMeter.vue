<script setup>
import { computed } from 'vue';

const props = defineProps({
  angle: {
    type: Number,
    default: 0,
  },
});

const clampedAngle = computed(() => {
  if (Number.isNaN(props.angle)) return -90;
  const angle = ((props.angle / 600) * 90) - 90;
  return Math.min(angle, 0);
});
</script>

<template>
  <div class="wrap">
    <div class="speed-meter">
      <div class="circle">
        <div class="needle" :style="`transform: rotate(${clampedAngle}deg)`" />
      </div>
    </div>
  </div>
</template>

<style scoped>
.wrap {
    border: 3px solid #000;
    height: 90px;
    width: 200px;
    overflow: hidden;
}

.speed-meter {
    transform: rotate(42deg);
    position: relative;
    left: 20px;
}

.circle {
    position: relative;
    width: 140px;
    height: 130px;
    border-radius: 100% 0 0 0;
    border: 3px solid black;
    border-bottom: 0;
    border-right: 0;
}

.needle {
    background: black;
    height: 7px;
    right: 13px;
    position: absolute;
    bottom: 5px;
    width: 0;
    transform-origin: 50% 100%;
    margin: -105px -4px 0;
    padding: 105px 4px 0;
    z-index: 2;
    border-radius: 50% 50% 0 0;
    transform: rotate(-90deg);

    /* animation: pendulum; */
    animation-iteration-count: infinite;
    animation-duration: 4s;

}

#speed-meter {
    background: #171717;
    margin: 100px auto;
    width: 600px;
    height: 600px;
    box-shadow: inset 0px 0px 9px 3px rgba(0, 0, 0, 1);
    border: 5px solid #171717;
    border-radius: 100%;
    display: block;
    position: relative;
}

@keyframes pendulum {
    10% {
        transform: rotate(0deg);
    }

    11% {
        transform: rotate(-2deg);
    }

    12% {
        transform: rotate(-4deg);
    }

    13% {
        transform: rotate(-2deg);
    }

    14% {
        transform: rotate(-4deg);
    }

    15% {
        transform: rotate(-2deg);
    }

    16% {
        transform: rotate(-4deg);
    }

    17% {
        transform: rotate(-2deg);
    }

    18% {
        transform: rotate(-4deg);
    }

    19% {
        transform: rotate(-2deg);
    }
}
</style>
