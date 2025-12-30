<script setup>
import { defineProps, onMounted, ref, watchEffect } from "vue";
import Chart from "chart.js/auto";

const props = defineProps({
  chartData: Object,
});

const chartCanvas = ref(null);

const getTextColor = () => {
  return (localStorage.getItem('color-theme') || 'dark') === "dark" ? "#ffffff" : "#000000";
};
const getTextColorReverse = () => {
  return (localStorage.getItem('color-theme') || 'dark') !== "dark" ? "#ffffff" : "#1f2937";
};

onMounted(() => {
  if (!chartCanvas.value || !props.chartData) return;

  new Chart(chartCanvas.value, {
    type: "pie",
    data: {
      labels: Object.keys(props.chartData),
      datasets: [
        {
          data: Object.values(props.chartData),
          backgroundColor: ["#FFCD56", "#36A2EB", "#FF6384", "#0dd933", "#009688", "#bb0000", "#ff9800", "#790dd9"],
          borderColor: getTextColorReverse(),
          borderWidth: 2,
          hoverOffset: 10,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      layout: {
        padding: 5
      },
      plugins: {
        legend: {
          labels: {
            color: getTextColor(),
            font: {
              size: 14,
              weight: "bold",
            },
            padding: 5,
            boxWidth: 20,
          },
          position: window.innerWidth < 640 ? "bottom" : "right",
        },
        tooltip: {
          backgroundColor: "rgba(0, 0, 0, 0.7)",
          titleColor: "#ffffff",
          bodyColor: "#ffffff",
          borderWidth: 1,
          borderColor: "#ffffff",
          padding: 10,
          cornerRadius: 8,
        },
      },
      animation: {
        animateScale: true,
        animateRotate: true,
      },
    },
  });

});

</script>

<template>
  <div>
    <canvas ref="chartCanvas"></canvas>
  </div>
</template>
