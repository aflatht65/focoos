<%@ page contentType="text/html; charset=UTF-8" %>

<%@ page import="focoos.dao.*,focoos.entities.*,java.util.List,java.util.Date,java.util.Calendar,java.text.DateFormat,java.text.SimpleDateFormat" %>

<%
	if(session.getAttribute("userData") == null) {
		response.sendRedirect("login.jsp?redirectTo="+request.getRequestURL().toString());
	}
	DateFormat df = new SimpleDateFormat("dd-MM-yyyy");
	DateFormat dfd = new SimpleDateFormat("dd/MM");
	Calendar nowCal = Calendar.getInstance();
	nowCal.set(Calendar.DAY_OF_WEEK, Calendar.MONDAY);
	String startDateStr = request.getParameter("startDate") != null ? request.getParameter("startDate") : df.format(nowCal.getTime());
	Date startDate = df.parse(startDateStr);
	DeadlinesDao deadlinesDao = new DeadlinesDao();
	deadlinesDao.open();
	List<Post> deadlines = deadlinesDao.getDeadlinesByDate(startDate);
	deadlinesDao.close();
%>

<%@ include file="includes/header.inc.jsp" %>

<div class="row full-width">
	<%@ include file="includes/sidemenu.inc.jsp" %>
	
	<div class="large-10 columns main">
		<%
			Calendar prevCal = Calendar.getInstance();
			prevCal.setTime(startDate);
			prevCal.add(Calendar.DATE, -7);
			Calendar nextCal = Calendar.getInstance();
			nextCal.setTime(startDate);
			nextCal.add(Calendar.DATE, +7);
		%>
		<h5>
			<a href="calendar.jsp?startDate=<%= df.format(prevCal.getTime()) %>" class="calendar-previous">Προηγούμενη</a>
			<a href="calendar.jsp?startDate=<%= df.format(nextCal.getTime()) %>" class="calendar-next">Επόμενη</a>
		</h5>
		<table>
		  <thead>
			<tr>
			<%
				Calendar cal = Calendar.getInstance();
				cal.setTime(startDate);
				cal.set(Calendar.DAY_OF_WEEK, Calendar.MONDAY);
			%>
			  <th width="150">Δευτέρα <%= dfd.format(cal.getTime()) %><% cal.add(Calendar.DATE, 1); %></th>
			  <th width="150">Τρίτη <%= dfd.format(cal.getTime()) %><% cal.add(Calendar.DATE, 1); %></th>
			  <th width="150">Τετάρτη <%= dfd.format(cal.getTime()) %><% cal.add(Calendar.DATE, 1); %></th>
			  <th width="150">Πέμπτη <%= dfd.format(cal.getTime()) %><% cal.add(Calendar.DATE, 1); %></th>
			  <th width="150">Παρασκευή <%= dfd.format(cal.getTime()) %><% cal.add(Calendar.DATE, 1); %></th>
			  <th width="150">Σάββατο <%= dfd.format(cal.getTime()) %><% cal.add(Calendar.DATE, 1); %></th>
			  <th width="150">Κυριακή <%= dfd.format(cal.getTime()) %><% cal.add(Calendar.DATE, 1); %></th>
			</tr>
		  </thead>
		  <tbody>
			<%
			for(Post post: deadlines) {
				Deadline deadline = (Deadline)post;
				Calendar dowCal = Calendar.getInstance();
				dowCal.setTime(deadline.deadlineDate);
			%>
				<tr>
				  <% for(int i = 1; i <= 7; i++) { %>
					  <% if((dowCal.get(Calendar.DAY_OF_WEEK)-1) == i) { %>
						<td><%= deadline.message %><BR /><small><%= deadline.lesson.name %></small></td>
					  <% } else { %>
						<td></td>
					  <% } %>
				  <% } %>
				</tr>
			<% } %>
		  </tbody>
		</table>
	</div>
</div>

<%@ include file="includes/footer.inc.jsp" %>