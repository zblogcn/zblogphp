Public Class Form1


    Dim n As Process = Nothing

    Dim p As Process = Nothing

    Dim m As Process = Nothing

    Private Sub Button1_Click(sender As Object, e As EventArgs) Handles Button1.Click
        If IsNothing(n) = True Then
            n = New Process
            n.StartInfo.FileName = "nginx.exe"
            n.StartInfo.WorkingDirectory = TextBox1.Text
            n.StartInfo.CreateNoWindow = True
            n.Start()
            'n.WaitForExit()
            Label1.Text = Label1.Text + n.Id.ToString
            Label1.ForeColor = Color.Green


        Else
            Label1.ForeColor = Color.Red
            Label1.Text = "Nginx: "
            Process.Start("taskkill", "/im nginx.exe /f")
            n = Nothing
        End If
    End Sub


    Private Sub Button2_Click(sender As Object, e As EventArgs) Handles Button2.Click
        If IsNothing(p) = True Then

            p = New Process
            p.StartInfo.FileName = "php-cgi.exe"
            p.StartInfo.WorkingDirectory = TextBox2.Text
            p.StartInfo.CreateNoWindow = True
            p.StartInfo.Arguments = "-b 127.0.0.1:9000"
            p.StartInfo.WindowStyle = ProcessWindowStyle.Hidden
            p.Start()

            Label2.Text = Label2.Text + p.Id.ToString
            Label2.ForeColor = Color.Green
        Else

            Label2.ForeColor = Color.Red
            Label2.Text = "PHP 5.3: "
            Process.Start("taskkill.exe", "/im php-cgi.exe /f")
            p.Kill()
            p = Nothing
        End If
    End Sub




    Private Sub Form1_FormClosed(sender As Object, e As FormClosedEventArgs) Handles Me.FormClosed
        If IsNothing(n) = False Then Process.Start("taskkill.exe", "/im nginx.exe /f")
        If IsNothing(p) = False Then Process.Start("taskkill.exe", "/im php-cgi.exe /f")
        If IsNothing(m) = False Then Process.Start("taskkill.exe", "/im mysqld.exe /f")
    End Sub


    Private Sub Form1_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        Label1.Text = "Nginx: "
        Label2.Text = "PHP 5.3: "
        Label3.Text = "MySQL: "
    End Sub

    Private Sub Button3_Click(sender As Object, e As EventArgs) Handles Button3.Click
        If IsNothing(m) = True Then

            m = New Process
            m.StartInfo.FileName = "mysqld.exe"
            m.StartInfo.WorkingDirectory = TextBox3.Text
            m.StartInfo.CreateNoWindow = True
            m.StartInfo.Arguments = "--defaults-file=" + TextBox3.Text + "my.ini --standalone"
            m.StartInfo.WindowStyle = ProcessWindowStyle.Hidden
            m.Start()

            Label3.Text = Label3.Text + m.Id.ToString
            Label3.ForeColor = Color.Green
        Else
            Label3.ForeColor = Color.Red
            Label3.Text = "MySQL: "
            Process.Start("taskkill.exe", "/im php-cgi.exe /f")
            m.Kill()
            m = Nothing
        End If
    End Sub
End Class