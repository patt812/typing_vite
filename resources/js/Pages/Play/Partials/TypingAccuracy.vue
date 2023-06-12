<script setup>
  import { computed } from 'vue';

  const props = defineProps({
    accuracy: {
      type: Number,
      default: 0,
    },
  });

  const offset = computed(() => {
    if (props.accuracy <= 0) {
      return 251;
    }
    if (props.accuracy >= 100) {
      return 0;
    }
    return ((100 - props.accuracy) * 251) / 100;
  });
</script>

<template>
  <div class="box select-none" :style="`stroke-dashoffset: ${offset};`">
    <div class="percent" :style="`stroke-dashoffset: ${offset};`">
      <svg>
        <circle class="base" cx="45" cy="45" r="40" />
        <circle class="line" cx="45" cy="45" r="40" :style="`stroke-dashoffset: ${offset};`" />
      </svg>
      <div class="number">
        <div class="title">
          <span class="!text-2xl">{{ accuracy.toString().split('.')[0] }}</span>
          <span class="!text-sm">
            {{
              accuracy.toString().split('.')[1] ? '.' + accuracy.toString().split('.')[1] : ''
            }}</span
          >
          <span class="!text-sm">%</span>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
  .box {
    position: relative;
    min-width: 100px;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-pack: center;
    -ms-flex-pack: center;
    justify-content: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
    -ms-flex-direction: column;
    flex-direction: column;
  }

  .box .percent svg {
    position: relative;
    width: 90px;
    height: 90px;
    -webkit-transform: rotate(-90deg);
    transform: rotate(-90deg);
  }

  .box .percent svg circle {
    position: relative;
    fill: none;
    stroke-width: 7;
    stroke: #f3f3f3;
    stroke-dasharray: 251;
    stroke-dashoffset: 0;
    stroke-linecap: round;
  }

  .box .percent .number {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-box-pack: center;
    -ms-flex-pack: center;
    justify-content: center;
    color: #111;
  }

  .box .percent .number .title {
    font-size: 1.8rem;
  }

  .box .percent .number .title span {
    font-size: 1rem;
  }

  .box .text {
    padding: 10px 0 0;
    text-align: center;
    font-weight: bold;
    font-size: 14px;
  }

  .box .percent .line {
    -webkit-animation: circleAnim 1s forwards;
    animation: circleAnim 1s forwards;
  }

  .box .percent .line {
    stroke-dashoffset: 0;
    stroke: #000;
  }

  @-webkit-keyframes circleAnim {
    0% {
      stroke-dasharray: 0 251;
    }

    99.9%,
    to {
      stroke-dasharray: 251 251;
    }
  }

  @keyframes circleAnim {
    0% {
      stroke-dasharray: 0 251;
    }

    99.9%,
    to {
      stroke-dasharray: 251 251;
    }
  }
</style>
