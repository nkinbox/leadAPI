<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Call Logs</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        .pointer {
            cursor: pointer
        }
        [v-cloak] {
            display: none;
        }

    </style>
</head>
<body>
    <nav class="navbar navbar-light bg-primary py-0 mb-3">
        <a class="navbar-brand" href="https://www.tripclues.in/leadAPI/public/callLogs">
            <img src="https://www.tripclues.in/images/tripclues-logo.jpg" height="50" alt="">
        </a>
    </nav>
    <div id="app" class="row no-gutters" v-cloak>
        <div class="col-3">
            <div class="m-2">
                <select v-model="department" class="form-control">
                    <option v-for="dept in departments" :value="dept.id" v-text="dept.name+' ('+dept.total_agents+')'"></option>
                </select>
            </div>
            <div v-if="agents.length == 0" class="text-center p-3">
                <div class="spinner-border text-secondary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
            <div v-else>
                <ul class="list-group">
                    <li class="list-group-item pointer d-flex" v-for="agent in filtered_agents" :key="agent.user_name" :class="{'bg-success text-white':(agent.user_name == selected_agent.user_name)}" @click="selectAgent(agent)">
                        <div v-text="agent.user_name" class="pr-2"></div>
                        <div v-text="agent.name"></div>
                        <div class="flex-grow-1 text-right">
                            <label v-text="agent.department_name" class="badge badge-secondary"></label>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-9">
            <div class="p-2">
                <table class="table table-sm text-center">
                    <thead>
                        <tr>
                            <th scope="col" colspan="5">
                                Date: <input type="date" v-model="date">
                            </th>
                        </tr>
                        <tr>
                            <th scope="col" @click="setFilter()" class="pointer"><span :class="{'border p-1 bg-secondary text-white':('allall0'==selectedFilter)}">#ALL</span></th>
                            <th scope="col" colspan="2" class="bg-warning">Incoming (@{{seconds(totalDurations.incoming)}})</th>
                            <th scope="col" colspan="2" class="bg-info text-white">Outgoing (@{{seconds(totalDurations.outgoing)}})</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row">Total</th>
                            <td class="pointer bg-warning" @click="setFilter('incoming', 'all')"><span v-text="stats.incoming.total.total" :class="{'border p-1 bg-secondary text-white':('incomingall0'==selectedFilter)}">0</span></td>
                            <td class="bg-warning" v-text="Object.keys(stats.incoming.total.unique).length">0</td>
                            <td class="pointer bg-info text-white" @click="setFilter('outgoing', 'all')"><span v-text="stats.outgoing.total.total" :class="{'border p-1 bg-secondary text-white':('outgoingall0'==selectedFilter)}">0</span></td>
                            <td class="bg-info text-white" v-text="Object.keys(stats.outgoing.total.unique).length">0</td>
                        </tr>
                        <tr>
                            <th scope="row">Received</th>
                            <td class="pointer bg-warning" @click="setFilter('incoming', 1)"><span v-text="stats.incoming.received.total" :class="{'border p-1 bg-secondary text-white':('incoming10'==selectedFilter)}">0</span></td>
                            <td class="bg-warning" v-text="Object.keys(stats.incoming.received.unique).length">0</td>
                            <td class="pointer bg-info text-white" @click="setFilter('outgoing', 1)"><span v-text="stats.outgoing.received.total" :class="{'border p-1 bg-secondary text-white':('outgoing10'==selectedFilter)}">0</span></td>
                            <td class="bg-info text-white" v-text="Object.keys(stats.outgoing.received.unique).length">0</td>
                        </tr>
                        <tr>
                            <th scope="row">Missed</th>
                            <td  class="pointer bg-warning" @click="setFilter('incoming', 0)"><span v-text="stats.incoming.missed.total"  :class="{'border p-1 bg-secondary text-white':('incoming00'==selectedFilter)}">0</span></td>
                            <td  class="bg-warning" v-text="Object.keys(stats.incoming.missed.unique).length">0</td>
                            <td  class="pointer bg-info text-white" @click="setFilter('outgoing', 0)"><span  v-text="stats.outgoing.missed.total" :class="{'border p-1 bg-secondary text-white':('outgoing00'==selectedFilter)}">0</span></td>
                            <td  class="bg-info text-white" v-text="Object.keys(stats.outgoing.missed.unique).length">0</td>
                        </tr>
                    </tbody>
                </table>
                <div v-if="loading_call_logs" class="text-center p-3">
                    <div class="spinner-border text-secondary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div v-else>
                    <div v-if="call_logs.length">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Employee</th>
                                    <th scope="col">Conversed</th>
                                    <th scope="col">Call Type</th>
                                    <th scope="col">Duration</th>
                                    <th scope="col">Time</th>
                                </tr>
                            </thead>
                            <tbody class="text-white">
                                <tr v-for="(log, index) in filtered_call_logs" :key="index" :class="{'bg-danger':(log.status?false:true), 'bg-success':(log.status?true:false)}">
                                    <th scope="row" v-text="index+1"></th>
                                    <td class="small" v-html="log.agent_name+'<br>'+log.agent_phone_number"></td>
                                    <td class="small" v-html="(log.saved_name?log.saved_name:'NA')+'<br>'+log.dial_code+' '+log.phone_number"></td>
                                    <td>
                                        <div v-text="log.call_type" class="badge font-weight-normal" :class="{'badge-info':(log.call_type == 'outgoing'), 'badge-warning':(log.call_type == 'incoming' || log.call_type == 'missed')}"></div>
                                    </td>
                                    <td v-text="seconds(log.duration)"></td>
                                    <td v-text="moment(log.timestamp, 'YYYY-MM-DD HH-mm-ss').format('hh:mm A, DD MMM ')"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else-if="selected_agent.user_name" class="alert alert-danger text-center h5 p-3">
                        No Record Found
                    </div>
                    <div v-else class="alert alert-danger text-center h5 p-3">
                        No Executive selected to fetch Call Records
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.24.0/min/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.0/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue"></script>
    <script>
        var app = new Vue({
            el: '#app',
            data() {
                return {
                    totalDurations: {
                        incoming: 0,
                        outgoing: 0
                    },
                    selected_agent: {
                        user_name: '',
                        name: '',
                        department_id: 0
                    },
                    agents: [],
                    date: '',
                    filter: {
                        type: 'all',
                        status: 'all',
                        unique: false
                    },
                    call_logs: [],
                    loading_call_logs: false,
                    departments: [],
                    department: 0
                }
            },
            computed: {
                selectedFilter() {
                    return this.filter.type+this.filter.status+((this.filter.unique)?'1':'0')
                },
                filtered_call_logs() {
                    return this.call_logs.filter((log) => {
                        if(this.filter.type == 'incoming' && (log.call_type == 'incoming' || log.call_type == 'missed')) {
                            if(this.filter.status == 'all') {
                                return true
                            } else if(this.filter.status) {
                                return (log.status)?true:false
                            } else {
                                return log.status?false:true
                            }
                        } else if(this.filter.type == 'outgoing' && this.filter.type == log.call_type) {
                            if(this.filter.status == 'all') {
                                return true
                            } else if(this.filter.status) {
                                return (log.status)?true:false
                            } else {
                                return log.status?false:true
                            }
                        }
                        return (this.filter.type == 'all' && this.filter.status == 'all')
                    })
                },
                filtered_agents() {
                    return this.agents.filter((agent) => {
                        return (this.department == 0 || agent.department_id == this.department)
                    })
                },
                stats() {
                    this.totalDurations = {
                        incoming: 0,
                        outgoing: 0
                    }
                    return this.call_logs.reduce((stats, log) => {
                        if(log.call_type == 'missed') {
                            this.totalDurations.incoming += log.duration
                            stats.incoming.missed.total++
                            stats.incoming.total.total++
                            stats.incoming.missed.unique[log.phone_number] = 0
                            stats.incoming.total.unique[log.phone_number] = 0
                        } else if(log.call_type == 'incoming') {
                            this.totalDurations.incoming += log.duration
                            stats.incoming.received.total++
                            stats.incoming.total.total++
                            stats.incoming.received.unique[log.phone_number] = 0
                            stats.incoming.total.unique[log.phone_number] = 0
                        } else if(log.call_type == 'outgoing') {
                            this.totalDurations.outgoing += log.duration
                            stats.outgoing.total.total++
                            stats.outgoing.total.unique[log.phone_number] = 0
                            if(log.status) {
                                stats.outgoing.received.total++
                                stats.outgoing.received.unique[log.phone_number] = 0
                            } else {
                                stats.outgoing.missed.unique[log.phone_number] = 0
                                stats.outgoing.missed.total++
                            }
                        }
                        return stats
                    }, {
                        incoming: {
                            total: {
                                total: 0,
                                unique: {},
                            },
                            received: {
                                total: 0,
                                unique: {},
                            },
                            missed: {
                                total: 0,
                                unique: {},
                            }
                        },
                        outgoing: {
                            total: {
                                total: 0,
                                unique: {},
                            },
                            received: {
                                total: 0,
                                unique: {},
                            },
                            missed: {
                                total: 0,
                                unique: {},
                            }
                        }
                    })
                }
            },
            watch: {
                date: function(newVal, oldVal) {
                    this.fetchLogs()
                },
                department: function(newVal, oldVal) {
                    if(this.selected_agent.department_id != newVal) {
                        this.selected_agent = {
                            user_name: '',
                            name: '',
                            department_id: 0
                        }
                        this.fetchLogs()
                    }
                }
            },
            mounted() {
                this.date = moment().format('YYYY-MM-DD')
                axios.get('https://tripclues.in/leadAPI/public/api/logger/agents').then(response => {
                    this.agents = response.data.agents
                }).catch(error => {
                    console.log(error)
                })
                axios.get('https://tripclues.in/leadAPI/public/api/logger/departments').then(response => {
                    this.departments = response.data.departments
                }).catch(error => {
                    console.log(error)
                })
            },
            methods: {
                setFilter(type = 'all', status = 'all', unique = false) {
                    this.filter = {
                        type: type,
                        status: status,
                        unique: unique
                    }
                },
                seconds(duration) {
                    return new Date(duration * 1000).toISOString().substr(11, 8)
                },
                fetchLogs() {
                    let query = 'date='+(moment(this.date).format('YYYY-MM-DD'));
                    if(this.selected_agent.user_name) {
                        query += '&user_name='+this.selected_agent.user_name
                    }
                    if(this.department) {
                        query += '&department_id='+this.department
                    }
                    this.loading_call_logs = true
                    axios.get('https://tripclues.in/leadAPI/public/api/logger/display?'+query).then(response => {
                        this.call_logs = response.data.logs
                        this.loading_call_logs = false
                    }).catch(error => {
                        console.log(error)
                        this.loading_call_logs = false
                    })
                },
                selectAgent(agent) {
                    this.selected_agent = agent
                    this.filter = {
                        type: 'all',
                        status: 'all',
                        unique: false
                    }
                    this.department = agent.department_id
                    this.fetchLogs()
                }
            }
        })
    </script>
</body>
</html>