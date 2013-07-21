Public Class Form1


    Dim n As Process = Nothing

    Dim p As Process = Nothing


    Private Sub Button1_Click(sender As Object, e As EventArgs) Handles Button1.Click
        If IsNothing(n) = True Then
            n = New Process
            n.StartInfo.FileName = "nginx.exe"
            n.StartInfo.WorkingDirectory = "d:\nginx\"
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
            p.StartInfo.WorkingDirectory = "d:\php53\"
            p.StartInfo.CreateNoWindow = True
            p.StartInfo.Arguments = "-b 127.0.0.1:9000"
            p.StartInfo.WindowStyle = ProcessWindowStyle.Hidden
            p.Start()

            Label2.Text = Label2.Text + p.Id.ToString
            Label2.ForeColor = Color.Green
        Else
            p.Kill()
            p = Nothing
            Label2.ForeColor = Color.Red
            Label2.Text = "PHP 5.3: "
        End If
    End Sub




    Private Sub Form1_FormClosed(sender As Object, e As FormClosedEventArgs) Handles Me.FormClosed
        Process.Start("taskkill.exe", "/im nginx.exe /f")


        Process.Start("taskkill.exe", "/im php-cgi.exe /f")
    End Sub


    Private Sub Form1_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        Label1.Text = "Nginx: "
        Label2.Text = "PHP 5.3: "
    End Sub
End Class