        <aside class="sidebar-wrapper" data-simplebar="true">
            <div class="sidebar-header">
                <div>
                    <img src="{{ asset('assets/images/logo-icon.png') }}" class="logo-icon" alt="logo icon">
                </div>
                <div>
                    <h4 class="logo-text">Sahasra Labs</h4>
                </div>
                <div class="toggle-icon ms-auto"> <i class="bi bi-list"></i>
                </div>
            </div>
            <!--navigation-->
            <ul class="metismenu" id="menu">
                <li class="menu-label">Menu</li>
                <li>
                    <a href="{{ route('admin.dashboard.index') }}" class="">
                        <div class="parent-icon"><i class="bi bi-house-fill"></i>
                        </div>
                        <div class="menu-title">Dashboard</div>
                    </a>

                </li>


                <li>
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon"><i class="bi bi-basket2-fill"></i>
                        </div>
                        <div class="menu-title">Products</div>
                    </a>
                    <ul>
                        <li> <a href="{{ route('products.index') }}"><i class="bi bi-circle"></i>Products List</a>
                        </li>
                        <li> <a href="{{ route('products.categories.index') }}"><i
                                    class="bi bi-circle"></i>Categories</a>
                        </li>
                        <li> <a href="{{ route('products.attributes.index') }}"><i
                                    class="bi bi-circle"></i>Attributes</a>
                        </li>
                        <li> <a href="{{ route('products.tags.index') }}"><i class="bi bi-circle"></i>Tags</a>
                        </li>
                        <li> <a href="{{ route('products.inventory.index') }}"><i class="bi bi-circle"></i>Inventory</a>
                        </li>
                        <li> <a href="{{ route('products.reviews.index') }}"><i class="bi bi-circle"></i>Reviews</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon"><i class="fadeIn animated bx bx-dollar-circle"></i>
                        </div>
                        <div class="menu-title">Orders</div>
                    </a>
                    <ul>
                        <li> <a href="{{ route('orders.index') }}"><i class="bi bi-circle"></i>Order List</a>
                        </li>
                        <li> <a href="ecommerce-transactions.html"><i class="bi bi-circle"></i>Transactions</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon"><i class="fadeIn animated bx bx-user-voice"></i>
                        </div>
                        <div class="menu-title">Users</div>
                    </a>
                    <ul>

                        <li> <a href="{{ route('users.index') }}"><i class="bi bi-circle"></i>All Users</a>
                        </li>

                        <li> <a href="ecommerce-products-grid.html"><i class="bi bi-circle"></i>Customers </a>
                        </li>
                        {{-- <li> <a href="ecommerce-products-categories.html"><i class="bi bi-circle"></i>Staff Members</a>
                </li> --}}
                        <li> <a href="ecommerce-orders.html"><i class="bi bi-circle"></i>Roles & Permissions</a>
                        </li>
                        <li> <a href="ecommerce-orders-detail.html"><i class="bi bi-circle"></i> Import / Export</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon"><i class="fadeIn animated bx bx-paper-plane"></i>
                        </div>
                        <div class="menu-title">Marketing</div>
                    </a>
                    <ul>
                        <li> <a href="{{ route('marketing.campaigns.index') }}"><i class="bi bi-circle"></i>Campaigns
                            </a>
                        </li>
                        <li> <a href="{{ route('marketing.coupons.index') }}"><i class="bi bi-circle"></i>Coupons </a>
                        </li>
                        <li> <a href="{{ route('marketing.gift-cards.index') }}"><i class="bi bi-circle"></i>Gift
                                Cards</a>
                        </li>
                        <li> <a href="{{ route('marketing.loyalty.index') }}"><i class="bi bi-circle"></i>Loyalty
                                Program</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon"><i class="bx bx-line-chart"></i></div>
                        <div class="menu-title">Analytics</div>
                    </a>
                    <ul>
                        <li>
                            <a href="{{ route('analytics.sales') }}"
                                class="{{ request()->is('analytics/sales*') ? 'active' : '' }}">
                                <i class="bx bx-circle"></i>Sales Analytics
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('analytics.customers') }}"
                                class="{{ request()->is('analytics/customers*') ? 'active' : '' }}">
                                <i class="bx bx-circle"></i>Customer Analytics
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('analytics.products') }}"
                                class="{{ request()->is('analytics/products*') ? 'active' : '' }}">
                                <i class="bx bx-circle"></i>Product Analytics
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('analytics.reports') }}"
                                class="{{ request()->is('analytics/reports*') ? 'active' : '' }}">
                                <i class="bx bx-circle"></i>Reports
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="menu-label">System</li>
                <li>
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon"><i class="bi bi-gear-fill"></i>
                        </div>
                        <div class="menu-title">Settings</div>
                    </a>
                    <ul>
                        <li> <a href="ecommerce-products-list.html"><i class="bi bi-circle"></i> Store Settings</a>
                        </li>
                    </ul>
                </li>

            </ul>
            <!--end navigation-->
        </aside>
