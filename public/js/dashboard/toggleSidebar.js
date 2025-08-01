const toggleSidebar = document.getElementById('toggleSidebar');
const sidebar = document.getElementById('sidebar');
const icon = toggleSidebar.querySelector('i');

// Hàm cập nhật trạng thái sidebar
function updateSidebarState() {
    if (window.innerWidth > 768) {
        sidebar.classList.add('active');
        icon.classList.remove('fa-bars');
        icon.classList.add('fa-times');
    } else {
        sidebar.classList.remove('active');
        icon.classList.add('fa-bars');
        icon.classList.remove('fa-times');
    }
}

// Xử lý click toggle
toggleSidebar.addEventListener('click', (e) => {
    e.preventDefault();
    sidebar.classList.toggle('active');
    icon.classList.toggle('fa-bars');
    icon.classList.toggle('fa-times');
});

// Đóng sidebar khi nhấn ngoài trên mobile
document.addEventListener('click', (e) => {
    if (window.innerWidth <= 768 && !sidebar.contains(e.target) && !toggleSidebar.contains(e.target)) {
        sidebar.classList.remove('active');
        icon.classList.add('fa-bars');
        icon.classList.remove('fa-times');
    }
});

// Cập nhật trạng thái khi thay đổi kích thước màn hình
window.addEventListener('resize', () => {
    updateSidebarState();
});

// Cập nhật trạng thái ban đầu
updateSidebarState();