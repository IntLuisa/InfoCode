<script setup>
import dayjs from 'dayjs'
import { ref, computed, reactive, onMounted } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import DialogModal from '@/Components/DialogModal.vue'
import ConfirmationModal from '@/Components/ConfirmationModal.vue'
import { useForm } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import { usePage } from '@inertiajs/vue3'

const title = 'Calendário'
const view = ref('month')
const currentDate = ref(new Date())
const draggedAppointment = ref(null)
const showModal = ref(false)
const showConfirm = ref(false)
const page = usePage()

const props = defineProps({
    users: Array,
    events: Array,
    filters: Object,

})

const is_observer = usePage().props.auth.user.role === 'Observer';
const is_board = usePage().props.auth.user.role === 'Board';
const is_manager = usePage().props.auth.user.role === 'Manager';

const appointments = computed(() =>
  props.events.map(event => ({
    ...event,
    date: event.start_date?.substring(0, 10),
    startTime: event.start_date
      ? new Date(event.start_date).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
      : '',
    endTime: event.end_date
      ? new Date(event.end_date).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
      : '',
    color: '#3b82f6'
  }))
)

const statuses = ref([
  { id: 'sold', name: 'Vendido', color: 'bg-gray-400' },
  { id: 'send_to_factory', name: 'Enviar Pedido para Fábrica', color: 'bg-blue-500' },
  { id: 'to_invoice', name: 'Pedido para Faturar', color: 'bg-green-500' },

  { id: 'separation', name: 'Separar', color: 'bg-yellow-500' },
  { id: 'waiting_assembly', name: 'Aguardando Montagem', color: 'bg-orange-500' },
  { id: 'assembling', name: 'Em Montagem', color: 'bg-indigo-500' },

  { id: 'deliver_expenses', name: 'Entregar Despesas', color: 'bg-purple-500' },
  { id: 'completed', name: 'Concluído', color: 'bg-emerald-500' },

  { id: 'general_calendar', name: 'Calendário Geral', color: 'bg-gray-500' }
])


const currentYear = computed(() => currentDate.value.getFullYear())
const currentMonth = computed(() => currentDate.value.getMonth())

const currentPeriodText = computed(() => {
  if (view.value === 'year') {
    return currentYear.value
  } else if (view.value === 'month') {
    return months.value[currentMonth.value].name + ' ' + currentYear.value
  } else {
    return 'Todos os Compromissos'
  }
})

const months = computed(() => {
  const monthNames = [
    'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
    'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
  ]

  return monthNames.map((name, index) => {
    const firstDay = new Date(currentYear.value, index, 1)
    const lastDay = new Date(currentYear.value, index + 1, 0)
    const daysInMonth = lastDay.getDate()

    const prevMonthLastDay = new Date(currentYear.value, index, 0).getDate()
    const firstDayOfWeek = firstDay.getDay()

    const days = []

    for (let i = firstDayOfWeek - 1; i >= 0; i--) {
      days.push({
        day: prevMonthLastDay - i,
        date: new Date(currentYear.value, index - 1, prevMonthLastDay - i).toISOString().split('T')[0],
        isCurrentMonth: false
      })
    }

    for (let i = 1; i <= daysInMonth; i++) {
      days.push({
        day: i,
        date: new Date(currentYear.value, index, i).toISOString().split('T')[0],
        isCurrentMonth: true
      })
    }

    const totalCells = 42
    const nextMonthDays = totalCells - days.length
    for (let i = 1; i <= nextMonthDays; i++) {
      days.push({
        day: i,
        date: new Date(currentYear.value, index + 1, i).toISOString().split('T')[0],
        isCurrentMonth: false
      })
    }

    return {
      name,
      index,
      days
    }
  })
})

const currentMonthDays = computed(() => {
  const year = currentDate.value.getFullYear()
  const month = currentDate.value.getMonth()

  const firstDay = new Date(year, month, 1)
  const lastDay = new Date(year, month + 1, 0)
  const daysInMonth = lastDay.getDate()

  const prevMonthLastDay = new Date(year, month, 0).getDate()
  const firstDayOfWeek = firstDay.getDay()

  const days = []

  for (let i = firstDayOfWeek - 1; i >= 0; i--) {
    const date = new Date(year, month - 1, prevMonthLastDay - i)
    days.push({
      day: prevMonthLastDay - i,
      date: date.toISOString().split('T')[0],
      isCurrentMonth: false,
      dayOfWeek: date.getDay(),
      isLastRow: false
    })
  }

  for (let i = 1; i <= daysInMonth; i++) {
    const date = new Date(year, month, i)
    days.push({
      day: i,
      date: date.toISOString().split('T')[0],
      isCurrentMonth: true,
      dayOfWeek: date.getDay(),
      isLastRow: false
    })
  }

  const totalCells = 42 
  const nextMonthDays = totalCells - days.length
  for (let i = 1; i <= nextMonthDays; i++) {
    const date = new Date(year, month + 1, i)
    days.push({
      day: i,
      date: date.toISOString().split('T')[0],
      isCurrentMonth: false,
      dayOfWeek: date.getDay(),
      isLastRow: false
    })
  }

  for (let i = 35; i < 42; i++) {
    if (days[i]) days[i].isLastRow = true
  }

  return days
})

const setView = (newView) => (view.value = newView)

const previousPeriod = () => {
  if (view.value === 'year') {
    currentDate.value = new Date(currentDate.value.getFullYear() - 1, currentDate.value.getMonth(), 1)
  } else {
    currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() - 1, 1)
  }
}

const nextPeriod = () => {
  if (view.value === 'year') {
    currentDate.value = new Date(currentDate.value.getFullYear() + 1, currentDate.value.getMonth(), 1)
  } else {
    currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() + 1, 1)
  }
}

const isToday = (dateString) => {
  const today = dayjs().startOf('day');
  const date = dayjs(dateString).startOf('day'); 
  return today.isSame(date, 'day');
}



const getStatusText = (status) => {
  const statusObj = statuses.value.find(s => s.id === status)
  return statusObj ? statusObj.name : 'Desconhecido'
}

const formatDate = (dateString) => new Date(dateString).toLocaleDateString('pt-BR')
const formatTime = (timeString) => {
  if (!timeString) return null
  return timeString.slice(0, 5)
}

const dragEnd = (event) => {
  if (event.target && event.target.classList) event.target.classList.remove('dragging')
}

const openModal = (item = null) => {

    if (item) {
        form.id = item.id
        form.title = item.title
        form.description = item.description
        form.start_date = item.start_date
        form.end_date = item.end_date

        form.is_all_day = !!item.is_all_day
        form.is_montage_date = !!item.is_montage_date
        form.flexible_date = !!item.flexible_date

        form.recurrence_type = item.recurrence_type ?? 'none'
        form.recurrence_until = item.recurrence_until
        form.repeat_day_of_week = item.repeat_day_of_week
        form.repeat_day_of_month = item.repeat_day_of_month
        form.repeat_day_of_year = item.repeat_day_of_year

        form.tags = item.tags
        form.status = item.status

    } else {
        reset()
    }

    showModal.value = true
}

const reset = () => {
    form.reset()
    form.clearErrors()
    form.id = null
}

const form = useForm({
    title: "",
    description: "",
    start_date: "",
    end_date: "",

    flexible_date: false,
    is_all_day: true,
    is_montage_date: false,

    recurrence_type: 'none',
    recurrence_until: "",

    repeat_day_of_week: null,
    repeat_day_of_month: null,
    repeat_day_of_year: null,

    tags: "",
    status: "pending",

    start_time: "",
    end_time: "",
    priority: "",
    responsible_id: null

})

const submit = () => {
  if (!form.priority) {
    form.priority = 'low'
  }
  if (form.id) {
    form.put(route('calendar.update', form.id), {
      onSuccess: () => {
        showModal.value = false
        form.reset()
      }
    })
  } else {
    form.post(route('calendar.store'), {
      onSuccess: () => {
        showModal.value = false
        form.reset()
      }
    })
  }
}

const openMenu = ref(null)

const toggleMenu = (id) => {
  openMenu.value = openMenu.value === id ? null : id
}

const closeMenu = () => {
  openMenu.value = null

}
const update = (appointment) => {
    console.log(appointment.user?.role, appointment.color)

  closeMenu()

  form.id = appointment.id

  form.title = appointment.title ?? ''
  form.description = appointment.description ?? ''

  form.start_date = appointment.start_date
    ? appointment.start_date.substring(0, 10)
    : ''

  form.end_date = appointment.end_date
    ? appointment.end_date.substring(0, 10)
    : ''

  form.is_all_day = !!appointment.is_all_day
  form.flexible_date = !!appointment.flexible_date
  form.is_montage_date = !!appointment.is_montage_date

  form.recurrence_type = appointment.recurrence_type ?? 'none'
  form.recurrence_until = appointment.recurrence_until
    ? appointment.recurrence_until.substring(0, 10)
    : ''

  form.repeat_day_of_week = appointment.repeat_day_of_week
  form.repeat_day_of_month = appointment.repeat_day_of_month
  form.repeat_day_of_year = appointment.repeat_day_of_year

  form.tags = appointment.tags ?? ''
  form.status = appointment.status ?? 'pending'

  form.start_time = appointment.start_time ?? ''
  form.end_time = appointment.end_time ?? ''
  form.priority = appointment.priority ?? ''
  form.responsible_id = appointment.responsible_id ?? null

  showModal.value = true
}

const confirmDestroy = (appointment) => {
  closeMenu()

  if (!appointment || !appointment.id) return

  form.id = appointment.id
  form.title = appointment.title

  showConfirm.value = true
}

const destroy = (id) => {
  if (!id) return

  router.delete(route('calendar.destroy', id), {
    preserveScroll: true,
    onSuccess: () => {
      showConfirm.value = false
      form.reset()
    }
  })
}
const dragType = ref(null) 

const dragAppointmentDate = (event, appointment) => {
  draggedAppointment.value = appointment
  dragType.value = 'date'

  event.dataTransfer.effectAllowed = 'move'
  event.dataTransfer.setData('text/plain', appointment.id)
}

const dragAppointmentStatus = (event, appointment) => {
  draggedAppointment.value = appointment
  dragType.value = 'status'

  event.dataTransfer.effectAllowed = 'move'
  event.dataTransfer.setData('text/plain', appointment.id)
}

const resetDrag = () => {
  draggedAppointment.value = null
  dragType.value = null
}

const dropAppointment = (event, newDate) => {
  if (dragType.value !== 'date') return
  if (!draggedAppointment.value) return

  const appointment = draggedAppointment.value

  const start = dayjs(appointment.start_date)
  const end   = dayjs(appointment.end_date)
  const duration = end.diff(start, 'day')

  const newStart = dayjs(newDate)
  const newEnd   = newStart.add(duration, 'day')

  appointment.start_date = newStart.format('YYYY-MM-DD')
  appointment.end_date   = newEnd.format('YYYY-MM-DD')

  router.put(route('calendar.update', appointment.id), {
    start_date: appointment.start_date,
    end_date: appointment.end_date,
  })
  
  resetDrag()
}

const dropAppointmentStatus = (event, newStatus) => {
  event.preventDefault()

  if (dragType.value !== 'status') return
  if (!draggedAppointment.value) return

  const appointment = draggedAppointment.value
  const oldStatus = appointment.status

  appointment.status = newStatus

  router.put(
    route('calendar.update', appointment.id),
    { status: newStatus },
    {
      preserveScroll: true,
      onError: () => {
        appointment.status = oldStatus
      }
    }
  )

  resetDrag()
}

const getAppointmentsForDate = (date) => {
  return visibleAppointments.value.filter(appointment => {
    const start = dayjs(appointment.start_date)
    const end   = dayjs(appointment.end_date)
    const current = dayjs(date)

    return current.isSame(start, 'day')
        || current.isSame(end, 'day')
        || (current.isAfter(start, 'day') && current.isBefore(end, 'day'))
  })
}

const getAppointmentsByStatus = (status) => {
  return visibleAppointments.value.filter(app => app.status === status)
}

const filteredAppointments = computed(() => {
  if (filters.status) {
    return visibleAppointments.value
  }

  return visibleAppointments.value.filter(e => e.status !== 'completed')
})

const isOverdue = (appointment) => {
  if (appointment.status === 'completed') return false
  if (!appointment.end_date) return false

  const now = dayjs()
  const today = now.startOf('day')

  const endDate = dayjs(appointment.end_date).startOf('day')

  if (endDate.isBefore(today)) return true

  if (endDate.isSame(today)) {
    if (!appointment.end_time) return false

    const endDateTime = dayjs(
      `${appointment.end_date} ${appointment.end_time}`
    )

    return endDateTime.isBefore(now)
  }
  return false
}

const sortedAppointmentsByDate = computed(() => {
  const today = dayjs().startOf('day')

  return [...filteredAppointments.value].sort((a, b) => {
    return dayjs(a.start_date).diff(today) -
           dayjs(b.start_date).diff(today)
  })
})

const priorities = [
  { id: 'low', name: 'Baixa', class: 'bg-gray-100 text-gray-800' },
  { id: 'medium', name: 'Média', class: 'bg-yellow-100 text-yellow-800' },
  { id: 'high', name: 'Alta', class: 'bg-red-100 text-red-800' },
  { id: 'urgent', name: 'Urgente', class: 'bg-red-100 text-red-800' }
]

const getPriorityClass = (priority) => {
  const p = priorities.find(p => p.id === priority)
  return p ? p.class : 'bg-gray-100 text-gray-800'
}

const getPriorityText = (priority) => {
  const p = priorities.find(p => p.id === priority)
  return p ? p.name : 'Sem prioridade'
}
const countAppointmentsForDate = (date) => {
  return getAppointmentsForDate(date).length
}
const limitedAppointmentsForDate = (date, limit = 2) => {
  return getAppointmentsForDate(date).slice(0, limit)
}

const open = ref(false)
const clearFilters = () => {
  localStorage.removeItem(FILTERS_STORAGE_KEY)

  Object.assign(filters, {
    search: '',
    role: null,
    status: null,
    priority: null,
    time_range: null,
    recurrence_type: null,
  })

  router.get(route('calendar.index'), {}, {
    replace: true,
    preserveState: false,
    preserveScroll: true,
  })

  open.value = false
}


const applyFilters = () => {
  localStorage.setItem(
    FILTERS_STORAGE_KEY,
    JSON.stringify(filters)
  )

  router.get(route('calendar.index'), filters, {
    replace: true,
    preserveScroll: true,
    preserveState: true,
  })
  
  open.value = false
  router.reload({
    preserveScroll: true,
    preserveState: false,
  })

}
const filtersApplied = computed(() => {
  return Object.values(filters).some(v =>
    v !== null && v !== false && v !== '' && v !== 'false'
  )
})

const filters = reactive({
  search: page.props.filters?.search ?? '',
  role: page.props.filters?.role ?? null,

  status: page.props.filters?.status ?? null,
  priority: page.props.filters?.priority ?? null,

  time_range: page.props.filters?.time_range ?? null,
  recurrence_type: page.props.filters?.recurrence_type ?? null,
})

const hasActiveFilters = computed(() => {
  if (!filtersApplied.value) return false

  return Object.values(filters).some(v =>
    v !== null && v !== false && v !== '' && v !== 'false'
  )
})

const resultsCount = computed(() => props.events.length)
const translateFilterValue = (type, value) => {
  if (!value) return value

  const maps = {

    time_range: {
      this_week: 'esta semana',
      this_biweek: 'próximas 2 semanas',
      this_month: 'este mês',
      this_year: 'este ano',
    },

    recurrence_type: {
      none: 'sem recorrência',
      daily: 'diário',
      business_daily: 'dias uteis',
      biweekly: 'quinzenal',
      weekly: 'semanal',
      monthly: 'mensal',
      yearly: 'anual',
    },

    status: {
      sold: 'vendido',
      send_to_factory: 'enviar para fábrica',
      to_invoice: 'pedido para faturar',
      separation: 'separação',
      waiting_assembly: 'aguardando montagem',
      assembling: 'em montagem',
      deliver_expenses: 'entregar despesas',
      completed: 'concluído',
      general_calendar: 'calendário geral',
    },
    priority: {
      low: 'baixa',
      medium: 'média',
      high: 'alta',
      urgent: 'urgente',
    },
    role: {
      Board: 'Diretoria',
      Manager: 'Gerência',
      Consultant: 'Consultor',
      Assembler: 'Montador',
      'Assembly Manager': 'Chefe Montagem',
      Finance: 'Financeiro',
      'Financial Assistant': 'Assistente Financeiro',
      Observer: 'Observador',
    }
  }

  return maps[type]?.[value] ?? value
}

const filtersDescription = computed(() => {
  const parts = []

  if (filters.search) {
    parts.push(`busca: "${filters.search}"`)
  }

  if (filters.status) {
    parts.push(`status: ${translateFilterValue('status', filters.status)}`)
  }

  if (filters.priority) {
    parts.push(`prioridade: ${translateFilterValue('priority', filters.priority)}`)
  }

  if (filters.time_range) {
    parts.push(`período: ${translateFilterValue('time_range', filters.time_range)}`)
  }

  if (filters.recurrence_type) {
    parts.push(`recorrência: ${translateFilterValue('recurrence_type', filters.recurrence_type)}`)
  }
  if (filters.role) {
    parts.push(`hierarquia: ${translateFilterValue('role', filters.role)}`)
  }

  return parts.join(' • ')
})

const visibleAppointments = computed(() => {
  if (!filtersApplied.value) {
    return appointments.value
  }

  return appointments.value.filter(app => {

    if (filters.search) {
      const term = filters.search.toLowerCase()
      if (
        !app.title?.toLowerCase().includes(term) &&
        !app.description?.toLowerCase().includes(term)
      ) return false
    }

    if (filters.onlyActive && app.status === 'completed') {
      return false
    }

    if (filters.status && app.status !== filters.status) {
      return false
    }

    if (filters.priority && app.priority !== filters.priority) {
      return false
    }

    if (filters.time_range) {
      const start = dayjs(app.start_date)
      const now = dayjs()

      if (filters.time_range === 'this_week' && !start.isSame(now, 'week')) {
        return false
      }

      if (filters.time_range === 'this_biweek') {
        const diff = start.diff(now.startOf('week'), 'day')
        if (diff < 0 || diff > 14) return false
      }

      if (filters.time_range === 'this_month' && !start.isSame(now, 'month')) {
        return false
      }

      if (filters.time_range === 'this_year' && !start.isSame(now, 'year')) {
        return false
      }
    }

    if (filters.recurrence_type && app.recurrence_type !== filters.recurrence_type) {
      return false
    }
    if (filters.role && app.user?.role !== filters.role) {
      return false
    }

    return true
  })
})

const FILTERS_STORAGE_KEY = 'calendar_filters'

const restoredFromStorage = ref(false)
const restoreFiltersIfNeeded = () => {
  if (restoredFromStorage.value) return

  if (Object.values(page.props.filters || {}).some(v =>
    v !== null && v !== false && v !== '' && v !== 'false'
  )) return


  const saved = localStorage.getItem(FILTERS_STORAGE_KEY)
  if (!saved) return

  try {
    const parsed = JSON.parse(saved)

    restoredFromStorage.value = true

    router.get(route('calendar.index'), parsed, {
      replace: true,
      preserveState: true,
      preserveScroll: true,
    })
  } catch (e) {
    console.error('Erro ao restaurar filtros', e)
  }
}
const optionsRole = [
  { value: null, label: 'Todas' },
  { value: 'Board', label: 'Diretoria' },
  { value: 'Manager', label: 'Gerência' },
  { value: 'Consultant', label: 'Consultor' },
  { value: 'Assembler', label: 'Montador' },
  { value: 'Assembly Manager', label: 'Chefe Montagem' },
  { value: 'Finance', label: 'Financeiro' },
  { value: 'Financial Assistant', label: 'Assistente Financeiro' },
  { value: 'Observer', label: 'Observador' },
]

const scrollAccumulator = ref(0)
const scrollThreshold = 120  

const onWheelMonth = (event) => {
  scrollAccumulator.value += event.deltaY

  if (scrollAccumulator.value <= -scrollThreshold) {
    previousPeriod()
    scrollAccumulator.value = 0
  } else if (scrollAccumulator.value >= scrollThreshold) {
    nextPeriod()
    scrollAccumulator.value = 0
  }
}

onMounted(() => {
  const saved = localStorage.getItem(FILTERS_STORAGE_KEY)

  if (saved) {
    try {
      const parsed = JSON.parse(saved)

      Object.assign(filters, parsed)
    } catch (e) {
      console.error('Erro ao restaurar filtros', e)
    }
  }
  restoreFiltersIfNeeded()
  document.addEventListener('click', () => {
    openMenu.value = null
  })

})

</script>

<template>
  <AppLayout :title="title">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          {{ title }}
      </h2>
    </template>
    <template #content>
      <div class="h-full bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
        <div id="app" class="h-full flex flex-col">
          <header class="bg-white dark:bg-gray-800 shadow-sm py-4 px-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-0">
              <div class="flex items-center space-x-4">
                <div class="flex space-x-2">
                  <button @click="previousPeriod" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300">
                    <i class="fas fa-chevron-left"></i>
                  </button>
                  <button @click="nextPeriod" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300">
                    <i class="fas fa-chevron-right"></i>
                  </button>
                </div>
                <span class="text-lg font-medium text-gray-700 dark:text-gray-200">{{ currentPeriodText }}</span>
                <div>
                  <button @click="open = true;"
                    class="flex items-center text-white font-medium rounded-lg text-sm px-4 py-1.5 focus:outline-none border border-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
                    type="button">
                    <i class="fa-solid fa-filter me-2 text-lg"></i>
                      Filtros
                  </button>
                </div>
              </div>

              <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-4 text-white">
                <div v-if="!is_observer">
                  <button @click="openModal"
                    class="flex items-center text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-4 py-1.5 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800"
                    type="button">
                    <i class="fa-solid fa-calendar-plus me-2 text-lg"></i>
                      Novo
                  </button>
                </div>
                <div class="flex bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                  <button
                    @click="setView('year')"
                    :class="{'bg-white dark:bg-gray-600 shadow': view === 'year'}"
                    class="px-3 py-1 rounded-md text-sm font-medium transition-colors"
                  >
                    <i class="fas fa-calendar-alt mr-1"></i> Anual
                  </button>
                  <button
                    @click="setView('month')"
                    :class="{'bg-white dark:bg-gray-600 shadow': view === 'month'}"
                    class="px-3 py-1 rounded-md text-sm font-medium transition-colors"
                  >
                    <i class="fas fa-calendar-day mr-1"></i> Mensal
                  </button>
                  <button
                    @click="setView('kanban')"
                    :class="{'bg-white dark:bg-gray-600 shadow': view === 'kanban'}"
                    class="px-3 py-1 rounded-md text-sm font-medium transition-colors"
                  >
                    <i class="fas fa-table-columns mr-1"></i> Kanban
                  </button>
                  <button
                    @click="setView('table')"
                    :class="{'bg-white dark:bg-gray-600 shadow': view === 'table'}"
                    class="px-3 py-1 rounded-md text-sm font-medium transition-colors"
                  >
                    <i class="fas fa-table mr-1"></i> Tabela
                  </button>
                </div>

              </div>
            </div>
          </header>

          <main class="flex-1 p-6 overflow-auto">
            <div
              v-if="hasActiveFilters"
              class="mb-4 p-4 rounded-lg border bg-blue-50 border-blue-200 dark:bg-blue-900/30 dark:border-blue-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2"
            >
              <div class="text-sm text-blue-800 dark:text-blue-200">
                <template v-if="resultsCount > 0">
                  🔍 {{ resultsCount }} evento(s) encontrado(s)
                  <span v-if="filtersDescription"> — {{ filtersDescription }}</span>
                </template>

                <template v-else>
                  ⚠️ Nenhum evento encontrado para os filtros aplicados
                  <span v-if="filtersDescription"> — {{ filtersDescription }}</span>
                </template>
              </div>

              <div class="flex gap-2 mt-2 sm:mt-0">
                <button @click="clearFilters" class="text-sm font-medium text-blue-700 dark:text-blue-300 hover:underline px-3 py-1 border border-blue-200 rounded-lg dark:border-blue-600">
                  Limpar filtros
                </button>
              </div>
            </div>

            <div
              v-if="view === 'year'"
              class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6"
            >
              <div v-for="month in months" :key="month.index" class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-3">{{ month.name }} {{ currentYear }}</h3>
                <div class="grid grid-cols-7 gap-1">
                  <div v-for="day in ['D', 'S', 'T', 'Q', 'Q', 'S', 'S']" :key="day" class="text-center text-xs font-medium text-gray-500 dark:text-gray-400">
                    {{ day }}
                  </div>
                  <div
                    v-for="day in month.days"
                    :key="day.date"
                    class="h-8 flex items-center justify-center relative"
                    @dragover.prevent
                    @drop="dropAppointment($event, day.date)"
                  >
                    <span
                      :class="{
                        'text-gray-400 dark:text-gray-500': !day.isCurrentMonth,
                        'text-gray-900 dark:text-white': day.isCurrentMonth,
                        'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200': isToday(day.date)
                      }"
                      class="text-sm w-6 h-6 flex items-center justify-center rounded-full"
                    >
                      {{ day.day }}
                    </span>

                    <div class="absolute -bottom-1 flex justify-center items-center w-full">
  
                      <template v-if="countAppointmentsForDate(day.date) <= 2">
                        <div
                          v-for="appointment in limitedAppointmentsForDate(day.date)"
                          :key="appointment.id"
                          :style="{ backgroundColor: appointment.color }"
                          class="w-1.5 h-1.5 rounded-full mx-0.5"
                        ></div>
                      </template>

                      <template v-else>
                        <span class="text-[10px] font-semibold text-blue-500 dark:text-blue-400">
                          #{{ countAppointmentsForDate(day.date) }}
                        </span>
                      </template>

                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div v-else-if="view === 'month'" 
              @wheel.prevent="onWheelMonth" class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-x-auto
              scrollbar scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-700 scrollbar-track-gray-100 dark:scrollbar-track-gray-900">
              <div class="grid grid-cols-7 min-w-[900px] border-b border-gray-200 dark:border-gray-700">
                <div v-for="day in ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado']"
                    :key="day"
                    class="py-3 text-center font-medium text-gray-700 dark:text-gray-300">
                  {{ day }}
                </div>
              </div>
              <div class="grid grid-cols-7 min-w-[900px]">
                <div
                  v-for="day in currentMonthDays"
                  :key="day.date"
                  :class="{
                    'bg-gray-50 dark:bg-gray-900': !day.isCurrentMonth,
                    'border-r border-gray-200 dark:border-gray-700': (day.dayOfWeek !== 6),
                    'border-b border-gray-200 dark:border-gray-700': !day.isLastRow
                  }"
                  class="min-h-32 p-2 relative"
                  @dragover.prevent
                  @drop="dropAppointment($event, day.date)"
                >
                  <div class="flex justify-between items-center mb-1">
                    <span
                      :class="{
                        'text-gray-400 dark:text-gray-500': !day.isCurrentMonth,
                        'text-gray-900 dark:text-white': day.isCurrentMonth,
                        'bg-blue-500 text-white': isToday(day.date)
                      }"
                      class="text-sm w-6 h-6 flex items-center justify-center rounded-full"
                    >
                      {{ day.day }}
                    </span>

                    <span
                      v-if="countAppointmentsForDate(day.date) > 0"
                      class="text-xs px-1.5 py-0.5 rounded-full bg-primary-600 text-white"
                    >
                      {{ countAppointmentsForDate(day.date) }}
                    </span>
                  </div>

                  <div
                    class="space-y-1 overflow-y-auto pr-1
                      scrollbar scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-700 scrollbar-track-gray-100 dark:scrollbar-track-gray-900"
                    style="max-height: 10rem;"
                  >

                    <div
                      v-for="appointment in getAppointmentsForDate(day.date)"
                      :key="appointment.id"
                      :style="{ borderLeftColor: appointment.hierarchy_color }"
                      class="text-xs p-1.5 rounded border-l-4 bg-white dark:bg-gray-800 shadow-sm cursor-move"
                      draggable="true"
                      @dragstart="dragAppointmentDate($event, appointment)"

                    >
                      <div class="flex flex-row justify-between items-start  "
                        @mouseenter="openMenu = appointment.id"
                        @mouseleave="openMenu = null">

                        <div>
                          <div class="font-medium truncate text-gray-800 dark:text-gray-200">
                            {{ appointment.title }}
                          </div>
                          <div class="text-gray-500 dark:text-gray-400 truncate">
                            {{ formatTime(appointment.start_time) }} - {{ formatTime(appointment.end_time) }}
                          </div>
                        </div>

                        <div v-if="openMenu === appointment.id"
                          class="absolute right-0 mt-2 w-20 bg-slate-700 rounded shadow-lg z-50 ">
                          <span class="flex flex-row justify-between items-center p-2">
                            <button @click="update(appointment)" type="button"
                                class="inline-flex items-center px-2 pe-0 py-2 text-sm font-medium text-center text-white bg-primary-700 rounded-lg hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:focus:ring-primary-900">
                                <i class="fas fa-pen me-2"></i>
                            </button>

                            <button @click="confirmDestroy(appointment)" type="button"
                                class="inline-flex items-center px-2 pe-0 py-2 text-sm font-medium text-center text-white bg-red-600 rounded-lg hover:bg-red-800 focus:ring-4 focus:ring-red-300 dark:focus:ring-red-900">
                                <i class="fas fa-trash me-2"></i>
                            </button>
                          </span>
                        </div>

                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div v-else-if="view === 'kanban'" class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden w-full relative overflow-x-auto
              scrollbar scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-700 scrollbar-track-gray-100 dark:scrollbar-track-gray-900">
              <div class="grid gap-6 p-6 grid-flow-col auto-cols-[280px] pr-8">

                <div
                  v-for="status in statuses"
                  :key="status.id"
                  class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 status-group"
                  @dragover.prevent
                  @drop="dropAppointmentStatus($event, status.id)"
                >
                  <h3 class="font-semibold text-gray-800 dark:text-white mb-3 flex items-center">
                    <span :class="status.color" class="w-3 h-3 rounded-full mr-2"></span>
                    {{ status.name }} ({{ getAppointmentsByStatus(status.id).length }})
                  </h3>

                  <div class="space-y-3">
                    <div
                      v-for="appointment in getAppointmentsByStatus(status.id)"
                      :key="appointment.id"
                      class="bg-white dark:bg-gray-800 p-3 rounded shadow-sm cursor-move"
                      draggable="true"
                      @dragstart="dragAppointmentStatus($event, appointment)"
                      @dragend="dragEnd"
                    >
                      <div class="flex justify-between items-start">
                        <h4 class="font-medium text-gray-800 dark:text-gray-200">{{ appointment.title }}</h4>
                      </div>
                      <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ appointment.description }}</p>
                      <div class="flex justify-between items-center mt-2 text-xs text-gray-500 dark:text-gray-400">
                        {{ formatDate(appointment.start_date) }}
                            <span v-if="appointment.end_date">
                              - {{ formatDate(appointment.end_date) }}
                            </span>
                            <span v-else>
                              -
                            </span>
                        <span>{{ formatTime(appointment.start_time) }} - {{ formatTime(appointment.end_time) }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div v-else-if="view === 'table'" class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
              <div class="p-6">
                <div class="overflow-x-auto -mx-4 sm:mx-0">

                  <table class="min-w-[900px] sm:min-w-full divide-y divide-gray-200 dark:divide-gray-700">

                    <thead class="bg-gray-50 dark:bg-gray-800 sticky top-0">
                      <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                          Título
                        </th>

                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                          Data
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                          Horário
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                          Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                          Prioridades
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                          Responsável
                        </th>

                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                          
                        </th>
                      </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <tr
                          v-for="appointment in sortedAppointmentsByDate"
                          :key="appointment.id"
                          class="hover:bg-gray-50 dark:hover:bg-gray-700 cursor-move"
                          draggable="true"
                          @dragstart="dragAppointment($event, appointment)"
                          @dragend="dragEnd"
                        >
                       
                          <td
                            class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white"
                            :class="{
                              'border-l-4 border-red-500 bg-red-50 dark:bg-red-900/10': isOverdue(appointment)
                            }"
                          >
                            <div >
                              <span class="font-semibold">{{ appointment.title }}</span>
                              <div class="text-sm text-gray-500 dark:text-gray-400">{{ appointment.description }}</div>
                            </div>
                          </td>

                          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ formatDate(appointment.start_date) }}
                            <span v-if="appointment.end_date">
                              - {{ formatDate(appointment.end_date) }}
                            </span>
                            <span v-else>
                              -
                            </span>

                          </td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ formatTime(appointment.start_time) }} - {{ formatTime(appointment.end_time) }}
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-xs px-2 py-1 rounded-full text-white" >
                              {{ getStatusText(appointment.status) }}
                            </span>
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-xs px-2 py-1 rounded-full" :class="getPriorityClass(appointment.priority)">
                              {{ getPriorityText(appointment.priority) || '-' }} 
                            </span>
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ appointment.responsible?.name || '-' }}
                          </td>
                          <td class="p-4 py-2 space-x-2 whitespace-nowrap text-end">
                            <button @click="update(appointment)" type="button"
                                class="inline-flex items-center px-2 pe-0 py-2 text-sm font-medium text-center text-white bg-primary-700 rounded-lg hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:focus:ring-primary-900">
                                <i class="fas fa-pen me-2"></i>
                            </button>

                            <button @click="confirmDestroy(appointment)" type="button"
                                class="inline-flex items-center px-2 pe-0 py-2 text-sm font-medium text-center text-white bg-red-600 rounded-lg hover:bg-red-800 focus:ring-4 focus:ring-red-300 dark:focus:ring-red-900">
                                <i class="fas fa-trash me-2"></i>
                            </button>
                          </td>
                        </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </main>
        </div>
      </div>
    </template>
  </AppLayout>
  <DialogModal :show="showModal" @close="showModal = false">
    <template #title>
        <h2 class="text-xl font-semibold mb-4">
            {{ form.id ? 'Editar Evento' : 'Adicionar Evento' }}
        </h2>
    </template>

    <template #content>
        <form ref="formHtml" @submit.prevent="submit">
            <div class="space-y-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                        Título do Evento
                    </label>
                    <input type="text" v-model="form.title"
                        class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg 
                        block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        required />
                </div>

                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                            Data de Início
                        </label>
                        <input type="date" v-model="form.start_date"
                            class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg 
                            block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            required />
                    </div>
                    <div>
                      <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                        Hora de Início
                      </label>
                      <input
                        type="time"
                        v-model="form.start_time"
                        class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg 
                          block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                      />
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                            Data de Término
                        </label>
                        <input type="date" v-model="form.end_date"
                            class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg 
                            block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            />
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                          Hora de Término
                        </label>
                        <input
                          type="time"
                          v-model="form.end_time"
                          class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg 
                            block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        />
                    </div>
                    
                </div>
                <div class="mt-4">
                  <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                    Repetição
                  </label>

                  <select v-model="form.recurrence_type"
                      class="w-full py-1.5 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                    <option value="none">Não repetir</option>
                    <option value="daily">Diária</option>
                    <option value="business_daily">Dias Úteis</option>
                    <option value="weekly">Semanal</option>
                    <option value="biweekly">Quinzenal</option>
                    <option value="monthly">Mensal</option>
                    <option value="yearly">Anual</option>
                  </select>
                </div>

                <div v-if="form.recurrence_type !== 'none'" class="grid grid-cols-2 gap-4 mt-4">
                  <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">Repetir até</label>
                    <input type="date" v-model="form.recurrence_until"
                      class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg 
                            block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                  </div>

                  <div v-if="form.recurrence_type === 'weekly' || form.recurrence_type === 'biweekly'">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">Dia da semana</label>
                    <select v-model="form.repeat_day_of_week"
                        class="w-full py-2 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                      <option :value="0">Domingo</option>
                      <option :value="1">Segunda</option>
                      <option :value="2">Terça</option>
                      <option :value="3">Quarta</option>
                      <option :value="4">Quinta</option>
                      <option :value="5">Sexta</option>
                      <option :value="6">Sábado</option>
                    </select>
                  </div>

                  <div v-if="form.recurrence_type === 'monthly'">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">Dia do mês</label>
                    <input type="number" min="1" max="31"
                      v-model="form.repeat_day_of_month"
                      class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg 
                        block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"/>
                  </div>

                  <div v-if="form.recurrence_type === 'yearly'">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">Dia do ano</label>
                    <input type="number" min="1" max="366"
                      v-model="form.repeat_day_of_year"
                      class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg 
                        block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"/>
                  </div>
                </div>
                <div class="grid grid-cols-2 gap-4 ">
                  <div >
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                      Prioridade
                    </label>

                    <select
                      v-model="form.priority"
                      class="w-full py-1.5 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg
                        dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    >
                      <option value="low">Baixa</option>
                      <option value="medium">Média</option>
                      <option value="high">Alta</option>
                      <option value="urgent">Urgente</option>
                    </select>
                  </div>

                  <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                      Responsável 
                    </label>
                    <select
                      v-model="form.responsible_id"
                      class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg 
                            block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    >
                      <option value="">Selecione um responsável</option>

                      <option
                        v-for="user in users"
                        :key="user.id"
                        :value="user.id"
                      >
                        {{ user.name }}
                      </option>
                    </select>

                  </div>

                </div>

                <div class="grid grid-cols-2 gap-4 mt-4">
                  <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                      Tags
                    </label>
                    <input type="text"
                      v-model="form.tags"
                      placeholder="ex: reunião, interno, urgente"
                      class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg 
                          block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"/>
                  </div>

                  <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                      Status
                    </label>

                    <select v-model="form.status"
                        class="w-full py-1.5 bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                      
                      <option value="sold"> Vendido</option>
                      <option value="send_to_factory">Enviar para Fábrica</option>
                      <option value="to_invoice">Pedido para Faturar</option>
                      <option value="separation">Separação</option>
                      <option value="waiting_assembly">Aguardando Montagem</option>
                      <option value="assembling">Em Montagem</option>
                      <option value="deliver_expenses">Entregar Despesas</option>
                      <option value="completed">Concluído</option>
                      <option value="general_calendar">Calendário Geral</option>
                    </select>
                  </div>
                </div>

                <div class="mt-4">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-primary-200">
                        Descrição do Evento
                    </label>
                    <textarea v-model="form.description"
                        class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg 
                        block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        rows="6">
                    </textarea>
                </div>
            </div>
        </form>
    </template>

    <template #footer>
        <div class="flex w-full items-center justify-between">
            <button @click="showModal = false"
                class="text-white bg-red-700 hover:bg-red-800 font-medium rounded-lg text-sm px-5 py-2.5">
                Cancelar
            </button>

            <button @click="submit"
                class="text-white bg-green-700 hover:bg-green-800 font-medium rounded-lg text-sm px-5 py-2.5">
                Salvar
            </button>
        </div>
    </template>
  </DialogModal>

  <ConfirmationModal :show="showConfirm" @close="showConfirm = false" @confirm="destroy(form.id)">
    <template #title>
        Excluir Evento
    </template>
    <template #content>
        Deseja realmente excluir <strong class="text-red-600">{{ form.title }}</strong>?
    </template>
  </ConfirmationModal>

  <transition name="slide">
    <aside
      v-if="open"
      class="fixed inset-y-0 right-0 w-[380px] bg-[#0f172a] text-gray-200 shadow-xl z-50 flex flex-col"
    >
      <div class="flex items-center justify-between px-5 py-4 border-b border-gray-700">
        <div class="flex items-center gap-2">
          <i class="fa-solid fa-filter text-gray-400"></i>
          <h2 class="font-semibold text-lg">Filtros Avançados</h2>
        </div>
      </div>

      <div class="flex-1 overflow-y-auto px-5 py-4 space-y-4">

        <div>
          <label class="text-xs text-gray-400 uppercase">Busca</label>
          <input
            v-model="filters.search"
            type="text"
            placeholder="Buscar por título ou descrição..."
            class="w-full mt-2 rounded-md bg-gray-800 border border-gray-700 px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500"
          />
        </div>

        <div v-if="is_board || is_manager">
          <label class="text-xs text-gray-400 uppercase">Por Hierarquia</label>

          <select
            class="w-full mt-3 bg-gray-800 border border-gray-700 rounded-md px-3 py-2 text-sm"
            v-model="filters.role"
          >
            <option
              v-for="role in optionsRole"
              :key="role.value"
              :value="role.value"
            >
              {{ role.label }}
            </option>
          </select>
        </div>

        <div>
          <label class="text-xs text-gray-400 uppercase">Intervalo de tempo</label>
          <div class="grid grid-cols-2 gap-4 mt-2 text-sm ">
            <span><input type="radio" value="this_week" v-model="filters.time_range" /> Essa Semana</span>
            <span><input type="radio" value="this_biweek" v-model="filters.time_range" /> Essa Quinzena</span>
            <span><input type="radio" value="this_month" v-model="filters.time_range" /> Esse Mês</span>
            <span><input type="radio" value="this_year" v-model="filters.time_range" /> Esse Ano</span>
          </div>
        </div>
        <div>
          <label class="text-xs text-gray-400 uppercase">Repetição do evento</label>
          <select class="w-full mt-2 bg-gray-800 border border-gray-700 rounded-md px-3 py-2 text-sm"
            v-model="filters.recurrence_type">
            <option :value="null">Todos os tipos</option>
            <option value="none">Não repetir</option>
            <option value="daily">Diária</option>
            <option value="business_daily">Dias Úteis</option>
            <option value="weekly">Semanal</option>
            <option value="biweekly">Quinzenal</option>
            <option value="monthly">Mensal</option>
            <option value="yearly">Anual</option>
          </select>
        </div>

        <div>
          <label class="text-xs text-gray-400 uppercase">Status</label>

          <select class="w-full mt-2 bg-gray-800 border border-gray-700 rounded-md px-3 py-2 text-sm"
            v-model="filters.status">
            <option :value="null">Todos os status</option>
            <option value="sold">Vendido</option>
            <option value="send_to_factory">Enviar para Fábrica</option>
            <option value="to_invoice">Pedido para Faturar</option>
            <option value="separation">Separação</option>
            <option value="waiting_assembly">Aguardando Montagem</option>
            <option value="assembling">Em Montagem</option>
            <option value="deliver_expenses">Entregar Despesas</option>
            <option value="completed">Concluído</option>
            <option value="general_calendar">Calendário Geral</option>
          </select>

        </div>

        <div>
          <label class="text-xs text-gray-400 uppercase">Prioridade</label>

          <div class="grid grid-cols-3 gap-4 mt-2 text-sm">
            <span><input type="radio" value="low" v-model="filters.priority" /> Baixa</span>
            <span><input type="radio" value="medium" v-model="filters.priority" /> Média</span>
            <span><input type="radio" value="high" v-model="filters.priority" /> Alta</span>
            <span><input type="radio" value="urgent" v-model="filters.priority" /> Urgente</span>
          </div>
        </div>
      </div>

      <div class="border-t border-gray-700 px-5 py-4 flex justify-between">
        <button @click="clearFilters"
          class="text-sm text-gray-400 hover:text-white">
          Limpar tudo
        </button>
        <button @click="applyFilters"
          class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2 rounded-md text-sm">
          Aplicar filtros
        </button>
      </div>
    </aside>
  </transition>

</template>

