<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SmartPrint System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex">

    <!-- Sidebar Navigation -->
    <aside class="w-72 bg-slate-900 border-r border-slate-800 flex flex-col justify-between hidden md:flex shrink-0">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-8">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-amber-600 via-amber-500 to-yellow-400 flex items-center justify-center text-slate-950 font-bold text-xl shadow-lg shadow-amber-500/20">
                    <i class="fa-solid fa-print"></i>
                </div>
                <div>
                    <h1 class="font-extrabold text-white text-lg leading-tight">SmartPrint</h1>
                    <p class="text-xs text-amber-400 font-semibold uppercase tracking-wider">Admin Portal</p>
                </div>
            </div>

            <nav class="space-y-1.5">
                <a href="#dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-amber-500/10 text-amber-400 font-semibold text-sm transition">
                    <i class="fa-solid fa-chart-pie w-5"></i> Dashboard
                </a>
                <a href="#orders" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 text-slate-300 font-medium text-sm transition">
                    <i class="fa-solid fa-box-archive w-5"></i> Active Orders
                </a>
                <a href="#inventory" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 text-slate-300 font-medium text-sm transition">
                    <i class="fa-solid fa-boxes-stacked w-5"></i> Inventory
                </a>
                <a href="#services" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 text-slate-300 font-medium text-sm transition">
                    <i class="fa-solid fa-tags w-5"></i> Services & Prices
                </a>
                <a href="#history" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 text-slate-300 font-medium text-sm transition">
                    <i class="fa-solid fa-clock-rotate-left w-5"></i> Order History
                </a>
                <a href="#chat-section" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 text-slate-300 font-medium text-sm transition">
                    <i class="fa-solid fa-comments w-5"></i> Customer Chat
                </a>
            </nav>
        </div>

        <div class="p-6 border-t border-slate-800">
            <a href="{{ route('admin.login') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-red-500/10 text-red-400 font-medium text-sm transition">
                <i class="fa-solid fa-right-from-bracket w-5"></i> Logout
            </a>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <main class="flex-1 flex flex-col min-h-screen overflow-x-hidden">
        
        <header class="bg-slate-900/80 backdrop-blur-md border-b border-slate-800 px-6 py-4 flex justify-between items-center sticky top-0 z-20">
            <div>
                <h2 class="text-xl font-bold text-white">Executive Dashboard</h2>
                <p class="text-xs text-slate-400">Dimla Street, Purok 6, Anyatam, San Ildefonso, Bulacan</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="bg-slate-950 border border-amber-500/30 px-4 py-2 rounded-xl text-xs font-medium">
                    Total Revenue: <span class="text-amber-400 font-bold text-sm">₱{{ number_format($totalSales ?? 0, 2) }}</span>
                </div>
            </div>
        </header>

        <div class="p-6 lg:p-8 space-y-8 flex-1">
            
            <!-- Statistics Cards Grid -->
            <div id="dashboard" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl shadow-xl">
                    <div class="flex justify-between items-start mb-4">
                        <p class="text-xs text-slate-400 uppercase font-bold tracking-wider">Monthly Sales</p>
                        <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center font-bold">
                            <i class="fa-solid fa-peso-sign"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-extrabold text-white">₱{{ number_format($actualMonthlySales ?? 0, 2) }}</h3>
                    <p class="text-xs text-amber-400 mt-2 font-medium">Target: {{ round($monthlyPercentage ?? 0, 1) }}% of ₱25,000</p>
                </div>

                <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl shadow-xl">
                    <div class="flex justify-between items-start mb-4">
                        <p class="text-xs text-slate-400 uppercase font-bold tracking-wider">Active Orders</p>
                        <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center font-bold">
                            <i class="fa-solid fa-box-open"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-extrabold text-white">{{ count($activeOrders ?? []) }}</h3>
                    <p class="text-xs text-blue-400 mt-2 font-medium">Pending or In-progress</p>
                </div>

                <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl shadow-xl">
                    <div class="flex justify-between items-start mb-4">
                        <p class="text-xs text-slate-400 uppercase font-bold tracking-wider">Inventory Items</p>
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center font-bold">
                            <i class="fa-solid fa-boxes-stacked"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-extrabold text-white">{{ count($inventoryItems ?? []) }}</h3>
                    <p class="text-xs text-emerald-400 mt-2 font-medium">Managed supplies</p>
                </div>

                <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl shadow-xl">
                    <div class="flex justify-between items-start mb-4">
                        <p class="text-xs text-slate-400 uppercase font-bold tracking-wider">Active Services</p>
                        <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center font-bold">
                            <i class="fa-solid fa-tags"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-extrabold text-white">{{ count($servicesItems ?? []) }}</h3>
                    <p class="text-xs text-purple-400 mt-2 font-medium">Printing services available</p>
                </div>
            </div>

            <!-- Sales Performance Graph Section -->
            <div class="sp-chart-card">
                <div class="sp-chart-head">
                    <div>
                        <h3 class="sp-chart-title"><i class="fa-solid fa-chart-line"></i> Monthly Sales Performance</h3>
                        <p class="sp-chart-sub">Graphical overview of shop revenue throughout the year.</p>
                    </div>
                    <div class="sp-chart-stats">
                        <div class="sp-stat"><span>Total this year</span><b id="spChartTotal" class="sp-gold">₱0.00</b></div>
                        <div class="sp-stat"><span>Best month</span><b id="spChartBest">—</b></div>
                        <div class="sp-stat"><span>Monthly target</span><b>₱25,000</b></div>
                    </div>
                </div>

                <div class="sp-chart-wrap">
                    <canvas id="salesChart"></canvas>
                    <div id="spChartEmpty" class="sp-chart-empty" hidden>
                        <i class="fa-solid fa-chart-line"></i>
                        <p>Wala pang naitalang benta ngayong taon.</p>
                    </div>
                </div>
            </div>

            <!-- Active Orders Section -->
            <div id="orders" class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-white">Active Customer Orders</h3>
                        <p class="text-xs text-slate-400">Manage ongoing print requests, view customer files, and upload pickup proof.</p>
                    </div>
                </div>

                @if(isset($activeOrders) && count($activeOrders) > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-300">
                            <thead class="bg-slate-950 text-slate-400 uppercase text-xs">
                                <tr>
                                    <th class="p-3.5 rounded-l-xl">Order ID</th>
                                    <th class="p-3.5">Customer Name</th>
                                    <th class="p-3.5">Product / Service</th>
                                    <th class="p-3.5">Customer File</th>
                                    <th class="p-3.5">Total Amount</th>
                                    <th class="p-3.5">Status</th>
                                    <th class="p-3.5 rounded-r-xl text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">
                                @foreach($activeOrders as $order)
                                    <tr class="hover:bg-slate-800/50 transition">
                                        <td class="p-3.5 font-semibold text-white align-middle">#{{ $order->order_id }}</td>
                                        <td class="p-3.5 font-medium align-middle">{{ $order->full_name ?? 'Walk-in Customer' }}</td>
                                        <td class="p-3.5 align-middle">{{ $order->product_name ?? 'Custom Print' }}</td>
                                        <td class="p-3.5 align-middle">
                                            @php $fname = \App\Http\Controllers\Admin\AdminController::orderFileName($order); @endphp
                                            @if($fname)
                                                <a href="{{ route('order.file', ['order' => $order->order_id]) }}" target="_blank" class="text-amber-400 hover:underline inline-flex items-center gap-1.5 text-xs font-semibold whitespace-nowrap">
                                                    <i class="fa-solid fa-file-arrow-down"></i> Tingnan ang File
                                                </a>
                                            @else
                                                <span class="text-slate-500 text-xs italic">Walang inupload</span>
                                            @endif
                                        </td>
                                        <td class="p-3.5 text-amber-400 font-bold align-middle whitespace-nowrap">₱{{ number_format($order->total ?? 0, 2) }}</td>
                                        
                                        <!-- Animated Status Card (clickable) -->
                                        <td class="p-3.5 align-middle">
                                            @php
                                                $st = trim($order->status ?? '');
                                                if ($st === '') { $st = 'Pending'; }

                                                if ($st === 'Printing' || $st === 'In Progress') {
                                                    $next = 'Ready for Pickup';
                                                } elseif ($st === 'Ready for Pickup') {
                                                    $next = null;
                                                } else {
                                                    $next = 'Printing';
                                                }
                                            @endphp

                                            <form action="{{ route('admin.orders.update') }}" method="POST" class="m-0">
                                                @csrf
                                                <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                                                <input type="hidden" name="status" value="{{ $next }}">

                                                <button type="{{ $next ? 'submit' : 'button' }}"
                                                        class="pp-click {{ $next ? '' : 'pp-click-done' }}"
                                                        title="{{ $next ? 'I-click para ilipat sa: ' . $next : 'Gamitin ang Complete para tapusin ang order' }}"
                                                        {{ $next ? '' : 'disabled' }}>

                                                    @if($st === 'Printing' || $st === 'In Progress')
                                                        <div class="pp-anim flex items-center gap-3 p-2.5 rounded-xl border border-amber-500/30 bg-amber-500/10 min-w-[190px]">
                                                            <div class="pp-printer">
                                                                <div class="pp-paper"></div>
                                                                <div class="pp-body"><i class="fa-solid fa-print"></i></div>
                                                            </div>
                                                            <div class="flex-1 text-left">
                                                                <div class="text-xs font-bold text-amber-400 flex items-center">
                                                                    Printing<span class="pp-dots"><b></b><b></b><b></b></span>
                                                                </div>
                                                                <div class="pp-bar mt-1.5"><span></span></div>
                                                            </div>
                                                        </div>
                                                    @elseif($st === 'Ready for Pickup')
                                                        <div class="pp-anim pp-ready flex items-center gap-3 p-2.5 rounded-xl border min-w-[190px]">
                                                            <div class="pp-icon">
                                                                <div class="pp-core"><i class="fa-solid fa-bag-shopping pp-hop"></i></div>
                                                                <div class="pp-check"><i class="fa-solid fa-check"></i></div>
                                                                <i class="fa-solid fa-sparkles pp-spark s1"></i>
                                                                <i class="fa-solid fa-star pp-spark s2"></i>
                                                                <i class="fa-solid fa-sparkles pp-spark s3"></i>
                                                            </div>
                                                            <div class="text-left">
                                                                <div class="text-xs font-bold text-purple-300">Ready for Pickup</div>
                                                                <div class="text-[10px] text-purple-400">Naka-ready na ang print</div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="pp-anim pp-wait flex items-center gap-3 p-2.5 rounded-xl border min-w-[190px]">
                                                            <div class="pp-icon">
                                                                <div class="pp-ring"></div>
                                                                <div class="pp-ring r2"></div>
                                                                <div class="pp-core"><i class="fa-solid fa-hourglass-half pp-flip"></i></div>
                                                            </div>
                                                            <div class="text-left">
                                                                <div class="text-xs font-bold text-sky-300">{{ $st }}</div>
                                                                <div class="pp-queue mt-1.5">
                                                                    <span></span><span></span><span></span><span></span><span></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </button>
                                            </form>
                                        </td>

                                        <!-- Action Controls -->
                                        <td class="p-3.5 text-right align-middle">
                                            <div class="flex flex-col items-end gap-2">
                                                <form action="{{ route('admin.orders.update') }}" method="POST" class="flex items-center gap-1.5">
                                                    @csrf
                                                    <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                                                    <select name="status" onchange="this.form.submit()" class="bg-slate-950 border border-slate-700 text-slate-200 text-xs rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-amber-500">
                                                        <option value="Pending" {{ ($order->status ?? 'Pending') === 'Pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="Printing" {{ ($order->status ?? '') === 'Printing' ? 'selected' : '' }}>Printing</option>
                                                        <option value="Ready for Pickup" {{ ($order->status ?? '') === 'Ready for Pickup' ? 'selected' : '' }}>Ready for Pickup</option>
                                                    </select>
                                                </form>

                                                <form action="{{ route('admin.orders.update') }}" method="POST" enctype="multipart/form-data" class="flex items-center justify-end gap-2 flex-wrap">
                                                    @csrf
                                                    <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                                                    <input type="hidden" name="status" value="Successful Pickup">
                                                    <input type="file" name="proof_image" title="Proof of pickup" class="text-[10px] text-slate-400 file:mr-2 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-500 file:text-slate-950 hover:file:bg-amber-400 max-w-[175px]">
                                                    <button type="submit" class="bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold px-3 py-1.5 rounded-lg text-xs transition shadow-md whitespace-nowrap">
                                                        Complete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-10 text-slate-500 border border-dashed border-slate-800 rounded-xl">
                        <i class="fa-regular fa-folder-open text-3xl mb-2"></i>
                        <p class="text-sm">Wala pang active orders sa ngayon.</p>
                    </div>
                @endif
            </div>

            <!-- Inventory Management Section -->
            <div id="inventory" class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
                <h3 class="text-lg font-bold text-white mb-4"><i class="fa-solid fa-boxes-stacked text-emerald-400 mr-2"></i> Inventory Management</h3>
                @if(isset($inventoryItems) && count($inventoryItems) > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($inventoryItems as $item)
                            <div class="bg-slate-950 border border-slate-800 p-4 rounded-xl flex justify-between items-center">
                                <div>
                                    <h4 class="font-bold text-white text-sm">{{ $item->item_name }}</h4>
                                    <p class="text-xs text-slate-400">{{ $item->category }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-amber-400 font-bold text-base">{{ $item->stock_quantity }}</span>
                                    <p class="text-[10px] text-slate-500 uppercase">{{ $item->unit }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-slate-500 italic">Walang inventory items na nakarehistro.</p>
                @endif
            </div>

            <!-- Services & Prices Section -->
            <div id="services" class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
                <h3 class="text-lg font-bold text-white mb-4"><i class="fa-solid fa-tags text-purple-400 mr-2"></i> Services & Prices</h3>
                @if(isset($servicesItems) && count($servicesItems) > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($servicesItems as $serv)
                            <div class="bg-slate-950 border border-slate-800 p-4 rounded-xl">
                                <h4 class="font-bold text-white text-sm">{{ $serv->service_name }}</h4>
                                <p class="text-xs text-slate-400 mt-1">{{ $serv->description }}</p>
                                <p class="text-amber-400 font-bold text-sm mt-3">₱{{ number_format($serv->price, 2) }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-slate-500 italic">Walang nakatalang serbisyo.</p>
                @endif
            </div>

            <!-- Completed Order History Section -->
            <div id="history" class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
                <h3 class="text-lg font-bold text-white mb-4"><i class="fa-solid fa-clock-rotate-left text-blue-400 mr-2"></i> Completed Order History</h3>
                @if(isset($orderHistory) && count($orderHistory) > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-300">
                            <thead class="bg-slate-950 text-slate-400 uppercase text-xs">
                                <tr>
                                    <th class="p-3.5">Order ID</th>
                                    <th class="p-3.5">Customer</th>
                                    <th class="p-3.5">Service</th>
                                    <th class="p-3.5">Total</th>
                                    <th class="p-3.5">Status</th>
                                    <th class="p-3.5">Proof of Pickup</th>
                                    <th class="p-3.5 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">
                                @foreach($orderHistory as $hist)
                                    <tr class="hover:bg-slate-800/50 transition">
                                        <td class="p-3.5 font-semibold text-white align-middle">#{{ $hist->order_id }}</td>
                                        <td class="p-3.5 align-middle">{{ $hist->full_name ?? 'Customer' }}</td>
                                        <td class="p-3.5 align-middle">{{ $hist->product_name }}</td>
                                        <td class="p-3.5 text-amber-400 font-bold align-middle">₱{{ number_format($hist->total, 2) }}</td>
                                        <td class="p-3.5 text-emerald-400 font-semibold align-middle">{{ $hist->status ?? 'Successful Pickup' }}</td>
                                        <td class="p-3.5 align-middle">
                                            @if(!empty($hist->proof_image))
                                                <a href="{{ asset('uploads/' . $hist->proof_image) }}" target="_blank" class="inline-block overflow-hidden rounded-lg border border-slate-700 hover:border-amber-400 transition">
                                                    <img src="{{ asset('uploads/' . $hist->proof_image) }}" alt="Proof" class="w-12 h-12 object-cover">
                                                </a>
                                            @else
                                                <span class="text-slate-500 text-xs italic">Walang litrato</span>
                                            @endif
                                        </td>
                                        <td class="p-3.5 text-right align-middle">
                                            <form action="{{ route('admin.orders.delete') }}" method="POST" onsubmit="return confirm('Sigurado ka bang gusto mong burahin ang order na ito sa history?');">
                                                @csrf
                                                <input type="hidden" name="order_id" value="{{ $hist->order_id }}">
                                                <button type="submit" class="text-rose-400 hover:text-rose-300 text-xs font-semibold">
                                                    <i class="fa-solid fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-slate-500 italic">Wala pang nakatalang completed order history.</p>
                @endif
            </div>

            <!-- Customer Live Chat Section sa Admin Panel -->
            <div id="chat-section" class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
                <h3 class="text-lg font-bold text-white mb-4"><i class="fa-solid fa-comments text-amber-400 mr-2"></i> Customer Live Chat Support</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Customer List -->
                    <div class="bg-slate-950 border border-slate-800 rounded-xl p-4 overflow-y-auto h-96">
                        <h4 class="text-xs font-bold uppercase text-slate-400 mb-3">Mga Customer</h4>
                        <div class="space-y-2">
                            @forelse($customersWithChats ?? [] as $cust)
                                <a href="{{ route('admin.index', ['tab' => 'messages', 'chat_user' => $cust->user_id]) }}#chat-section" 
                                   class="block p-3 rounded-lg transition {{ ($selectedChatUser == $cust->user_id) ? 'bg-amber-500/20 border border-amber-500/40 text-amber-300' : 'bg-slate-900 hover:bg-slate-800 text-slate-300' }}">
                                    <div class="font-bold text-sm">{{ $cust->full_name }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $cust->email }}</div>
                                </a>
                            @empty
                                <p class="text-xs text-slate-500 italic">Wala pang nagpapadala ng mensahe.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Chat Messages Area -->
                    <div class="md:col-span-2 bg-slate-950 border border-slate-800 rounded-xl p-4 flex flex-col h-96">
                        @if($selectedChatUser)
                            <div id="adminChatMessages" class="flex-1 overflow-y-auto space-y-3 mb-4 pr-2">
                                @foreach($chatMessages as $msg)
                                    <div class="flex flex-col {{ $msg->sender === 'admin' ? 'items-end' : 'items-start' }}">
                                        <div class="max-w-[75%] p-3 rounded-xl text-xs {{ $msg->sender === 'admin' ? 'bg-amber-500 text-slate-950 font-medium' : 'bg-slate-800 text-slate-200' }}">
                                            {{ $msg->message }}
                                            @if(!empty($msg->attachment))
                                                <div class="mt-2">
                                                    <a href="{{ asset('uploads/' . $msg->attachment) }}" target="_blank" class="text-blue-300 underline text-[10px] block">Tingnan ang Kalakip na File</a>
                                                </div>
                                            @endif
                                        </div>
                                        <span class="text-[9px] text-slate-500 mt-1">{{ $msg->created_at }}</span>
                                    </div>
                                @endforeach
                            </div>

                            <!-- AJAX Send Message Form (Hindi na magre-reload ang page) -->
                            <form id="adminChatForm" action="{{ route('admin.messages.send') }}" method="POST" enctype="multipart/form-data" class="flex gap-2 items-center pt-2 border-t border-slate-800">
                                @csrf
                                <input type="hidden" name="customer_id" value="{{ $selectedChatUser }}">
                                <input type="text" name="message" placeholder="Mag-type ng tugon sa customer..." class="flex-1 bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                                <input type="file" name="chat_file" class="text-[10px] text-slate-400 max-w-[130px]">
                                <button type="submit" class="bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold px-4 py-2 rounded-xl text-xs transition">
                                    <i class="fa-solid fa-paper-plane"></i>
                                </button>
                            </form>
                        @else
                            <div class="flex-1 flex items-center justify-center text-slate-500 text-sm italic">
                                Pumili ng customer sa kaliwa para simulan ang pakikipag-chat.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </main>

    <script>
        window.monthlySalesData = @json($monthlySalesArray ?? array_fill(0, 12, 0));
    </script>
    <script src="{{ asset('js/admin.js') }}"></script>
</body>
</html>